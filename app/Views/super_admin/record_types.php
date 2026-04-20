<?= $this->extend('layouts/auth_layout') ?>
<?= $this->section('content') ?>

<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">Record Types & OCR Keywords</h2>
  <p class="text-green-100">Manage what Record types are detected and what keywords trigger them</p>
</div>


<!-- Add New Type Button -->
<div class="mb-6">
  <button onclick="openTypeModal()"
          class="bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
    Add Document Type
  </button>
</div>

<!-- Types Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
  <table class="w-full">
    <thead class="bg-gray-50 border-b border-gray-200">
      <tr>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Key Name</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Label</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Filename Suffix</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Keywords</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Active</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Order</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      <?php foreach ($types as $type): ?>
      <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 text-sm font-mono text-gray-700"><?= esc($type['key_name']) ?></td>
        <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= esc($type['label']) ?></td>
        <td class="px-6 py-4 text-sm text-gray-500 font-mono text-xs"><?= esc($type['filename_suffix']) ?></td>
        <td class="px-6 py-4">
          <button onclick="openKeywordsModal(<?= $type['type_id'] ?>, '<?= esc($type['label']) ?>')"
                  class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 font-semibold">
            <?= $type['keyword_count'] ?> keywords
          </button>
        </td>
        <td class="px-6 py-4">
          <span class="px-2 py-1 text-xs rounded-full <?= $type['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' ?>">
            <?= $type['is_active'] ? 'Active' : 'Inactive' ?>
          </span>
        </td>
        <td class="px-6 py-4 text-sm text-gray-600"><?= $type['sort_order'] ?></td>
        <td class="px-6 py-4">
          <div class="flex gap-3">
            <button onclick='openTypeModal(<?= json_encode($type) ?>)'
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm">Edit</button>
            <a href="<?= base_url('super-admin/record-types/delete-type/' . $type['type_id']) ?>"
               class="text-red-600 hover:text-red-800 font-medium text-sm"
               onclick="return confirm('Delete this document type and all its keywords?')">Delete</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- ── Add/Edit Type Modal ── -->
<div id="typeModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">
    <div class="flex items-center justify-between mb-5">
      <h3 id="typeModalTitle" class="text-xl font-bold text-gray-800">Add Document Type</h3>
      <button onclick="closeTypeModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form action="<?= base_url('super-admin/record-types/save-type') ?>" method="post" class="space-y-4">
      <input type="hidden" name="type_id" id="f_type_id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Key Name <span class="text-gray-400">(snake_case, no spaces)</span></label>
        <input type="text" name="key_name" id="f_key_name" required placeholder="e.g. transcript"
               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-gray-400">(shown in dropdowns)</span></label>
        <input type="text" name="label" id="f_label" required placeholder="e.g. Transcript Record"
               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filename Suffix <span class="text-gray-400">(used in saved filenames)</span></label>
        <input type="text" name="filename_suffix" id="f_suffix" required placeholder="e.g. Transcript_Record"
               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
          <input type="number" name="sort_order" id="f_sort_order" value="0" min="0"
                 class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select name="is_active" id="f_is_active"
                  class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeTypeModal()"
                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium">Cancel</button>
        <button type="submit"
                class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- ── Delete Keyword Confirmation Modal ── -->
<div id="deleteKeywordModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
    <div class="flex flex-col items-center text-center gap-3 mb-5">
      <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Delete Keyword?</h3>
      <p class="text-sm text-gray-500">You are about to delete the keyword:</p>
      <p id="deleteKeywordName" class="text-sm font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-lg"></p>
      <p class="text-sm text-gray-400">This action cannot be undone.</p>
    </div>
    <div class="flex gap-3">
      <button onclick="closeDeleteKeywordModal()"
              class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-sm">
        Cancel
      </button>
      <button id="confirmDeleteKeywordBtn"
              class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium text-sm">
        Yes, Delete
      </button>
    </div>
  </div>
</div>

<!-- ── Keywords Modal ── -->
<div id="keywordsModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-bold text-gray-800">Keywords</h3>
        <p id="kwModalSubtitle" class="text-sm text-gray-500"></p>
      </div>
      <button onclick="closeKeywordsModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Add keyword -->
    <div class="flex gap-2 mb-4">
      <input type="text" id="newKeywordInput" placeholder="e.g. transcript of records"
             class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
      <button onclick="addKeyword()"
              class="px-4 py-2 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium text-sm">Add</button>
    </div>

    <!-- Keyword list -->
    <div id="keywordList" class="flex-1 overflow-y-auto space-y-2">
      <p class="text-gray-400 text-sm text-center py-4">Loading…</p>
    </div>

    <div class="pt-4">
      <button onclick="closeKeywordsModal()"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-sm">Close</button>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let _currentTypeId    = null;
let _currentTypeLabel = null;

// ── Type Modal ──
function openTypeModal(type) {
  document.getElementById('f_type_id').value     = type ? type.type_id    : '';
  document.getElementById('f_key_name').value    = type ? type.key_name   : '';
  document.getElementById('f_label').value       = type ? type.label      : '';
  document.getElementById('f_suffix').value      = type ? type.filename_suffix : '';
  document.getElementById('f_sort_order').value  = type ? type.sort_order : 0;
  document.getElementById('f_is_active').value   = type ? type.is_active  : 1;
  document.getElementById('typeModalTitle').textContent = type ? 'Edit Document Type' : 'Add Document Type';
  document.getElementById('typeModal').classList.remove('hidden');
}
function closeTypeModal() { document.getElementById('typeModal').classList.add('hidden'); }

// ── Keywords Modal ──
function openKeywordsModal(typeId, typeLabel) {
  _currentTypeId  = typeId;
  _currentTypeLabel = typeLabel;
  document.getElementById('kwModalSubtitle').textContent = typeLabel;
  document.getElementById('keywordsModal').classList.remove('hidden');
  document.getElementById('newKeywordInput').value = '';
  loadKeywords();
}
function closeKeywordsModal() { document.getElementById('keywordsModal').classList.add('hidden'); }

function loadKeywords() {
  const list = document.getElementById('keywordList');
  list.innerHTML = '<p class="text-gray-400 text-sm text-center py-4">Loading…</p>';
  fetch('<?= base_url('super-admin/record-types/keywords/') ?>' + _currentTypeId + '?_=' + Date.now(), { cache: 'no-store' })
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('keywordList');
      if (!data.keywords.length) {
        list.innerHTML = '<p class="text-gray-400 text-sm text-center py-4">No keywords yet. Add one above.</p>';
      } else {
        list.innerHTML = data.keywords.map(kw => `
          <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg border border-gray-200">
            <span class="text-sm text-gray-800">${kw.keyword}</span>
            <button onclick="deleteKeyword(${kw.keyword_id}, '${kw.keyword.replace(/'/g, "\\'")}')"
                  class="text-red-400 hover:text-red-600 text-xs font-medium">✕</button>
          </div>`).join('');
      }

      // ── Update the badge on the main table row in real time ──
      const count = data.keywords.length;
      document.querySelectorAll('button[onclick]').forEach(btn => {
        if (btn.getAttribute('onclick') === `openKeywordsModal(${_currentTypeId}, '${_currentTypeLabel}')`) {
          btn.textContent = count + (count === 1 ? ' keyword' : ' keywords');
        }
      });
    });
}

function addKeyword() {
  const input  = document.getElementById('newKeywordInput');
  const kw     = input.value.trim();
  if (!kw) return;

  // Disable button and input to prevent double-submission while saving
  const btn = document.querySelector('button[onclick="addKeyword()"]');
  input.disabled = true;
  btn.disabled   = true;
  btn.textContent = 'Saving…';

  const fd = new FormData();
  fd.append('type_id', _currentTypeId);
  fd.append('keyword', kw);

  fetch('<?= base_url('super-admin/record-types/save-keyword') ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        input.value = '';
        loadKeywords(); // reloads list AND updates the badge in one call
      }
    })
    .catch(err => console.error('addKeyword failed:', err))
    .finally(() => {
      input.disabled  = false;
      btn.disabled    = false;
      btn.textContent = 'Add';
    });
}

let _pendingDeleteId = null;

function deleteKeyword(kwId, kwText) {
  _pendingDeleteId = kwId;
  document.getElementById('deleteKeywordName').textContent = kwText;
  document.getElementById('deleteKeywordModal').classList.remove('hidden');
}

function closeDeleteKeywordModal() {
  _pendingDeleteId = null;
  document.getElementById('deleteKeywordModal').classList.add('hidden');
}

document.getElementById('confirmDeleteKeywordBtn').addEventListener('click', () => {
  if (!_pendingDeleteId) return;
  const btn = document.getElementById('confirmDeleteKeywordBtn');
  btn.disabled = true;
  btn.textContent = 'Deleting…';

  const fd = new FormData();
  fetch('<?= base_url('super-admin/record-types/delete-keyword/') ?>' + _pendingDeleteId,
        { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        closeDeleteKeywordModal();
        loadKeywords();
      }
    })
    .catch(err => console.error('deleteKeyword failed:', err))
    .finally(() => {
      btn.disabled = false;
      btn.textContent = 'Yes, Delete';
    });
});

// Close delete modal on backdrop click
document.getElementById('deleteKeywordModal').addEventListener('click', e => {
  if (e.target === document.getElementById('deleteKeywordModal')) closeDeleteKeywordModal();
});

// Close on backdrop click
document.getElementById('typeModal').addEventListener('click', e => { if (e.target === document.getElementById('typeModal')) closeTypeModal(); });
document.getElementById('keywordsModal').addEventListener('click', e => { if (e.target === document.getElementById('keywordsModal')) closeKeywordsModal(); });

// Enter key in keyword input
document.getElementById('newKeywordInput').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addKeyword(); } });
</script>
<?= $this->endSection() ?>