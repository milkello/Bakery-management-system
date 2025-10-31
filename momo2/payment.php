<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Mobile Money Payment</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
  <div class="w-full max-w-lg bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Make a Payment</h1>

    <form id="paymentForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Phone (international or local)</label>
        <input id="phone" name="phone" type="text" placeholder="e.g. 237650000000 or +237650000000" required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Amount</label>
        <input id="amount" name="amount" type="number" min="1" step="0.01" placeholder="1000" required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Currency</label>
        <select id="currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
          <option value="XAF">XAF</option>
          <option value="EUR">EUR</option>
          <option value="USD">USD</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">External Reference (optional)</label>
        <input id="externalId" name="externalId" type="text" placeholder="ORDER-1234"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      </div>

      <div class="flex items-center space-x-2">
        <button id="submitBtn" type="submit"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">
          Pay now
        </button>
        <button id="resetBtn" type="button"
          class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
          Reset
        </button>
      </div>

      <div id="status" class="mt-4 text-sm"></div>
    </form>
  </div>

  <script>
    const form = document.getElementById('paymentForm');
    const statusEl = document.getElementById('status');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    resetBtn.addEventListener('click', () => {
      form.reset();
      statusEl.innerHTML = '';
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      statusEl.innerHTML = '<span class="text-gray-600">Initiating payment...</span>';
      submitBtn.disabled = true;

      const data = {
        phone: document.getElementById('phone').value.trim(),
        amount: document.getElementById('amount').value,
        currency: document.getElementById('currency').value,
        externalId: document.getElementById('externalId').value || ''
      };

      try {
        const res = await fetch('initiate_payment.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        const json = await res.json();

        if (res.ok) {
          statusEl.innerHTML = `
            <div class="p-3 rounded-md bg-green-50 border border-green-200 text-green-800">
              Payment request sent. Reference: <strong>${json.transactionReference || json.reference || json.referenceId || 'N/A'}</strong><br/>
              Message: ${json.message || json.status || 'Request accepted'}
            </div>`;
        } else {
          statusEl.innerHTML = `
            <div class="p-3 rounded-md bg-red-50 border border-red-200 text-red-800">
              Error: ${json.error || JSON.stringify(json)}
            </div>`;
        }
      } catch (err) {
        statusEl.innerHTML = `<div class="p-3 rounded-md bg-red-50 border border-red-200 text-red-800">Network error: ${err.message}</div>`;
      } finally {
        submitBtn.disabled = false;
      }
    });
  </script>
</body>
</html>
