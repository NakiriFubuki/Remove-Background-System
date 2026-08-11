/**
 * Remove Background System - Frontend application
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */

const state = {
  file: null,
  uploadId: null,
  originalUrl: null,
  resultUrl: null,
  resultBlob: null,
  processing: false,
};

const els = {
  imageInput: document.getElementById('imageInput'),
  originalPreview: document.getElementById('originalPreview'),
  resultPreview: document.getElementById('resultPreview'),
  removeBgBtn: document.getElementById('removeBgBtn'),
  downloadBtn: document.getElementById('downloadBtn'),
  resetBtn: document.getElementById('resetBtn'),
  fileBadge: document.getElementById('fileBadge'),
  statusBadge: document.getElementById('statusBadge'),
  progressBar: document.getElementById('progressBar'),
  progressFill: document.getElementById('progressFill'),
  progressText: document.getElementById('progressText'),
  historyGrid: document.getElementById('historyGrid'),
  historyEmpty: document.getElementById('historyEmpty'),
  manualModal: document.getElementById('manualModal'),
  openManualBtn: document.getElementById('openManualBtn'),
  openManualHeroBtn: document.getElementById('openManualHeroBtn'),
  toast: document.getElementById('toast'),
  workspace: document.getElementById('workspace'),
};

function showToast(message, isError = false) {
  els.toast.hidden = false;
  els.toast.textContent = message;
  els.toast.classList.toggle('is-error', isError);
  clearTimeout(showToast._timer);
  showToast._timer = setTimeout(() => {
    els.toast.hidden = true;
  }, 3200);
}

function setStatus(text, accent = false) {
  els.statusBadge.textContent = text;
  els.statusBadge.classList.toggle('badge--accent', accent);
}

function setProgress(visible, percent = 10, text = 'Working…') {
  els.progressBar.classList.toggle('is-hidden', !visible);
  els.progressFill.style.width = `${Math.max(5, Math.min(100, percent))}%`;
  els.progressText.textContent = text;
}

function openManual() {
  els.manualModal.hidden = false;
  document.body.style.overflow = 'hidden';
  const closeBtn = els.manualModal.querySelector('.modal__close');
  closeBtn?.focus();
}

function closeManual() {
  els.manualModal.hidden = true;
  document.body.style.overflow = '';
}

function renderPreview(container, src, emptyHtml) {
  if (!src) {
    container.innerHTML = emptyHtml;
    return;
  }
  container.innerHTML = '';
  const img = document.createElement('img');
  img.src = src;
  img.alt = 'Preview';
  container.appendChild(img);
}

const originalEmpty = els.originalPreview.innerHTML;
const resultEmpty = els.resultPreview.innerHTML;

function resetApp() {
  if (state.originalUrl) URL.revokeObjectURL(state.originalUrl);
  if (state.resultUrl) URL.revokeObjectURL(state.resultUrl);

  state.file = null;
  state.uploadId = null;
  state.originalUrl = null;
  state.resultUrl = null;
  state.resultBlob = null;
  state.processing = false;

  els.imageInput.value = '';
  els.fileBadge.textContent = 'No file';
  setStatus('Waiting', false);
  setProgress(false);
  els.removeBgBtn.disabled = true;
  els.resetBtn.disabled = true;
  els.downloadBtn.classList.add('is-disabled');
  els.downloadBtn.removeAttribute('href');
  els.downloadBtn.setAttribute('aria-disabled', 'true');

  renderPreview(els.originalPreview, null, originalEmpty);
  renderPreview(els.resultPreview, null, resultEmpty);
}

function enableDownload(url, filename) {
  els.downloadBtn.href = url;
  els.downloadBtn.download = filename || 'no-background.png';
  els.downloadBtn.classList.remove('is-disabled');
  els.downloadBtn.setAttribute('aria-disabled', 'false');
}

async function handleFile(file) {
  if (!file) return;

  const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
  if (!allowed.includes(file.type)) {
    showToast('Only JPG, PNG, and WEBP files are allowed.', true);
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    showToast('File is too large. Maximum size is 10 MB.', true);
    return;
  }

  if (state.originalUrl) URL.revokeObjectURL(state.originalUrl);
  if (state.resultUrl) URL.revokeObjectURL(state.resultUrl);

  state.file = file;
  state.uploadId = null;
  state.resultBlob = null;
  state.resultUrl = null;
  state.originalUrl = URL.createObjectURL(file);

  els.fileBadge.textContent = file.name.length > 28 ? `${file.name.slice(0, 25)}…` : file.name;
  setStatus('Ready', true);
  els.removeBgBtn.disabled = false;
  els.resetBtn.disabled = false;
  els.downloadBtn.classList.add('is-disabled');
  els.downloadBtn.setAttribute('aria-disabled', 'true');

  renderPreview(els.originalPreview, state.originalUrl);
  renderPreview(els.resultPreview, null, resultEmpty);

  try {
    const form = new FormData();
    form.append('image', file);
    const res = await fetch('api/upload.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success && data.data?.id) {
      state.uploadId = data.data.id;
    }
  } catch {
    // Upload history is optional; local processing still works.
  }
}

/**
 * Fallback remover: flood-fill from edges using color similarity.
 * Works best on solid / near-solid backgrounds.
 */
async function removeBackgroundFallback(file) {
  const bitmap = await createImageBitmap(file);
  const canvas = document.createElement('canvas');
  canvas.width = bitmap.width;
  canvas.height = bitmap.height;
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  ctx.drawImage(bitmap, 0, 0);

  const { width, height } = canvas;
  const imageData = ctx.getImageData(0, 0, width, height);
  const data = imageData.data;
  const visited = new Uint8Array(width * height);
  const queue = [];

  const samples = [
    [0, 0],
    [width - 1, 0],
    [0, height - 1],
    [width - 1, height - 1],
    [Math.floor(width / 2), 0],
    [Math.floor(width / 2), height - 1],
    [0, Math.floor(height / 2)],
    [width - 1, Math.floor(height / 2)],
  ];

  let br = 0;
  let bg = 0;
  let bb = 0;
  samples.forEach(([x, y]) => {
    const i = (y * width + x) * 4;
    br += data[i];
    bg += data[i + 1];
    bb += data[i + 2];
  });
  br = Math.round(br / samples.length);
  bg = Math.round(bg / samples.length);
  bb = Math.round(bb / samples.length);

  const threshold = 42;
  const softEdge = 28;

  const isBg = (idx) => {
    const i = idx * 4;
    const dr = data[i] - br;
    const dg = data[i + 1] - bg;
    const db = data[i + 2] - bb;
    return Math.sqrt(dr * dr + dg * dg + db * db) <= threshold + softEdge;
  };

  const push = (x, y) => {
    if (x < 0 || y < 0 || x >= width || y >= height) return;
    const idx = y * width + x;
    if (visited[idx]) return;
    if (!isBg(idx)) return;
    visited[idx] = 1;
    queue.push(idx);
  };

  samples.forEach(([x, y]) => push(x, y));

  while (queue.length) {
    const idx = queue.pop();
    const x = idx % width;
    const y = (idx - x) / width;
    const i = idx * 4;
    const dr = data[i] - br;
    const dg = data[i + 1] - bg;
    const db = data[i + 2] - bb;
    const dist = Math.sqrt(dr * dr + dg * dg + db * db);

    if (dist <= threshold) {
      data[i + 3] = 0;
    } else {
      const t = (dist - threshold) / softEdge;
      data[i + 3] = Math.round(Math.min(255, Math.max(0, t * 255)));
    }

    push(x + 1, y);
    push(x - 1, y);
    push(x, y + 1);
    push(x, y - 1);
  }

  ctx.putImageData(imageData, 0, 0);
  bitmap.close();

  return new Promise((resolve, reject) => {
    canvas.toBlob((blob) => {
      if (!blob) reject(new Error('Could not create result image.'));
      else resolve(blob);
    }, 'image/png');
  });
}

async function removeBackgroundAI(file, onProgress) {
  const { removeBackground } = await import(
    'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.5/+esm'
  );

  return removeBackground(file, {
    progress: (key, current, total) => {
      if (!total) return;
      const pct = Math.round((current / total) * 100);
      onProgress(Math.min(92, 15 + pct * 0.75), `Removing background… ${pct}%`);
    },
  });
}

async function processImage() {
  if (!state.file || state.processing) return;

  state.processing = true;
  els.removeBgBtn.disabled = true;
  setStatus('Processing', true);
  setProgress(true, 12, 'Starting background removal…');

  try {
    let blob;

    try {
      blob = await removeBackgroundAI(state.file, setProgress);
    } catch (aiError) {
      console.warn('AI removal unavailable, using fallback:', aiError);
      setProgress(true, 45, 'Using local fallback remover…');
      blob = await removeBackgroundFallback(state.file);
    }

    if (state.resultUrl) URL.revokeObjectURL(state.resultUrl);
    state.resultBlob = blob;
    state.resultUrl = URL.createObjectURL(blob);

    renderPreview(els.resultPreview, state.resultUrl);
    enableDownload(state.resultUrl, `no-background-${Date.now()}.png`);
    setProgress(true, 100, 'Done');
    setStatus('Completed', true);
    showToast('Background removed successfully.');

    // Persist processed PNG on server (optional history)
    try {
      const reader = new FileReader();
      const dataUrl = await new Promise((resolve, reject) => {
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
      });

      const form = new FormData();
      form.append('id', state.uploadId || 0);
      form.append('image_data', dataUrl);
      await fetch('api/save.php', { method: 'POST', body: form });
      loadHistory();
    } catch {
      // Non-blocking
    }

    setTimeout(() => setProgress(false), 700);
  } catch (error) {
    console.error(error);
    setStatus('Failed', false);
    setProgress(false);
    showToast('Could not remove background. Please try another image.', true);
  } finally {
    state.processing = false;
    els.removeBgBtn.disabled = !state.file;
  }
}

async function loadHistory() {
  try {
    const res = await fetch('api/history.php');
    const data = await res.json();
    const items = data.data || [];

    els.historyGrid.querySelectorAll('.history-card').forEach((el) => el.remove());

    if (!items.length) {
      els.historyEmpty.hidden = false;
      return;
    }

    els.historyEmpty.hidden = true;
    items.forEach((item) => {
      const card = document.createElement('article');
      card.className = 'history-card';
      card.innerHTML = `
        <a href="${item.processed_path}" download title="${item.original_name}">
          <img src="${item.processed_path}" alt="${item.original_name}">
          <span>${item.original_name}</span>
        </a>
      `;
      els.historyGrid.appendChild(card);
    });
  } catch {
    els.historyEmpty.hidden = false;
  }
}

function setupDragAndDrop() {
  const zone = els.originalPreview;

  ['dragenter', 'dragover'].forEach((evt) => {
    zone.addEventListener(evt, (e) => {
      e.preventDefault();
      zone.classList.add('is-drop-target');
    });
  });

  ['dragleave', 'drop'].forEach((evt) => {
    zone.addEventListener(evt, (e) => {
      e.preventDefault();
      zone.classList.remove('is-drop-target');
    });
  });

  zone.addEventListener('drop', (e) => {
    const file = e.dataTransfer?.files?.[0];
    if (file) handleFile(file);
  });
}

function setupManual() {
  els.openManualBtn?.addEventListener('click', openManual);
  els.openManualHeroBtn?.addEventListener('click', openManual);

  els.manualModal.querySelectorAll('[data-close-manual]').forEach((el) => {
    el.addEventListener('click', closeManual);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !els.manualModal.hidden) {
      closeManual();
    }
  });
}

els.imageInput.addEventListener('change', (e) => {
  const file = e.target.files?.[0];
  if (file) handleFile(file);
});

els.removeBgBtn.addEventListener('click', processImage);
els.resetBtn.addEventListener('click', resetApp);

setupDragAndDrop();
setupManual();
loadHistory();
