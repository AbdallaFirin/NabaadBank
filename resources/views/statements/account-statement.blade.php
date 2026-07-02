<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10.5px; color:#1a1a1a; background:#fff; }
  .page { padding:24px 28px; }

  .header { display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #0A2E5D; padding-bottom:10px; margin-bottom:14px; }
  .logo-fallback { font-size:18px; font-weight:bold; color:#0A2E5D; }
  .bank-sub  { font-size:9px; color:#888; margin-top:2px; }
  .report-title { text-align:right; }
  .report-title h2 { font-size:13px; font-weight:bold; color:#0A2E5D; }
  .report-title .meta { font-size:9px; color:#888; margin-top:3px; }

  /* Account info */
  .account-info { display:flex; gap:0; margin-bottom:14px; border:1px solid #d8e2f0; border-radius:4px; overflow:hidden; }
  .info-block { flex:1; padding:8px 12px; border-right:1px solid #d8e2f0; }
  .info-block:last-child { border-right:none; }
  .info-block .label { font-size:8px; color:#888; text-transform:uppercase; letter-spacing:.4px; }
  .info-block .value { font-size:11.5px; font-weight:bold; color:#0A2E5D; margin-top:2px; }

  /* Period bar */
  .period-bar { background:#f4f7fc; border:1px solid #d8e2f0; border-radius:4px; padding:6px 12px; margin-bottom:12px; font-size:9.5px; color:#555; display:flex; gap:20px; }
  .period-bar strong { color:#333; }

  /* Table */
  table { width:100%; border-collapse:collapse; margin-bottom:12px; }
  thead tr { background:#0A2E5D; color:#fff; }
  thead th { padding:6px 8px; text-align:left; font-size:9px; font-weight:600; }
  thead th.r { text-align:right; }
  tbody tr { border-bottom:1px solid #eef2f8; }
  tbody tr:nth-child(even) { background:#f9fafc; }
  tbody td { padding:5px 8px; font-size:9.5px; vertical-align:top; }
  tbody td.r { text-align:right; font-family:monospace; }
  tbody td.cr { color:#065f46; text-align:right; font-family:monospace; }
  tbody td.dr { color:#991b1b; text-align:right; font-family:monospace; }
  tbody td.muted { color:#888; }

  .totals-row td { font-weight:bold; background:#eef2fa; border-top:2px solid #0A2E5D; font-size:10px; }

  /* Summary */
  .summary { display:flex; gap:10px; margin-bottom:12px; }
  .summary-card { flex:1; border:1px solid #d8e2f0; border-radius:4px; padding:8px 10px; background:#f9fafc; }
  .summary-card .label { font-size:8px; color:#888; text-transform:uppercase; letter-spacing:.5px; }
  .summary-card .value { font-size:13px; font-weight:bold; color:#0A2E5D; margin-top:2px; }

  .footer { border-top:1px solid #d8e2f0; margin-top:14px; padding-top:8px; display:flex; justify-content:space-between; font-size:8.5px; color:#aaa; }

  .badge { display:inline-block; padding:1px 5px; border-radius:3px; font-size:8px; font-weight:600; }
  .badge-cr { background:#d1fae5; color:#065f46; }
  .badge-dr { background:#fee2e2; color:#991b1b; }
</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="header">
    <div>
      @php $logoPath = 'file://' . str_replace('\\', '/', public_path('images/logo.png')); @endphp
      @if(file_exists(public_path('images/logo.png')))
        <img src="{{ $logoPath }}" alt="NABAAD Bank" style="height:50px">
      @else
        <div class="logo-fallback">NABAAD Bank</div>
        <div class="bank-sub">Garowe Branch · Somalia</div>
      @endif
    </div>
    <div class="report-title">
      <h2>Account Statement</h2>
      <div class="meta">Generated: {{ now()->format('d M Y, H:i') }}</div>
      <div class="meta">Confidential — For account holder use only</div>
    </div>
  </div>

  <!-- Account Info -->
  <div class="account-info">
    <div class="info-block">
      <div class="label">Account Holder</div>
      <div class="value">{{ $account->customer->name }}</div>
    </div>
    <div class="info-block">
      <div class="label">Account Number</div>
      <div class="value" style="font-family:monospace">{{ $account->account_number }}</div>
    </div>
    <div class="info-block">
      <div class="label">Account Type</div>
      <div class="value">{{ $account->getTypeLabel() }}</div>
    </div>
    <div class="info-block">
      <div class="label">Currency</div>
      <div class="value">{{ $account->currency ?? 'USD' }}</div>
    </div>
    <div class="info-block">
      <div class="label">Current Balance</div>
      <div class="value">{{ number_format($account->balance, 2) }}</div>
    </div>
  </div>

  <!-- Period -->
  <div class="period-bar">
    <span>Statement Period: <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong></span>
    <span>Transactions: <strong>{{ $transactions->count() }}</strong></span>
  </div>

  <!-- Summary cards -->
  @php
    $credits = $transactions->where('type', '!=', 'withdrawal')->where('type', '!=', 'loan_repayment')->sum('amount');
    $debits  = $transactions->whereIn('type', ['withdrawal', 'loan_repayment'])->sum('amount');
    $openBal = $transactions->first()?->balance_before ?? $account->balance;
    $closeBal= $transactions->last()?->balance_after   ?? $account->balance;
  @endphp
  <div class="summary">
    <div class="summary-card"><div class="label">Opening Balance</div><div class="value">{{ number_format($openBal, 2) }}</div></div>
    <div class="summary-card"><div class="label">Total Credits</div><div class="value" style="color:#065f46">{{ number_format($credits, 2) }}</div></div>
    <div class="summary-card"><div class="label">Total Debits</div><div class="value" style="color:#991b1b">{{ number_format($debits, 2) }}</div></div>
    <div class="summary-card"><div class="label">Closing Balance</div><div class="value">{{ number_format($closeBal, 2) }}</div></div>
  </div>

  <!-- Transactions table -->
  @if($transactions->isEmpty())
    <p style="text-align:center;color:#888;padding:20px 0;font-size:10px">No transactions in this period.</p>
  @else
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Reference</th>
        <th>Description</th>
        <th>Type</th>
        <th class="r">Debit</th>
        <th class="r">Credit</th>
        <th class="r">Balance</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transactions as $txn)
      @php
        $isDebit = in_array($txn->type, ['withdrawal', 'loan_repayment', 'transfer']);
        $isCredit= in_array($txn->type, ['deposit', 'loan_disbursement', 'reversal']);
      @endphp
      <tr>
        <td class="muted">{{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y') }}</td>
        <td style="font-family:monospace;font-size:8.5px">{{ $txn->reference }}</td>
        <td style="max-width:160px">{{ Str::limit($txn->description ?? $txn->type, 45) }}</td>
        <td><span class="badge {{ $isDebit ? 'badge-dr' : 'badge-cr' }}">{{ strtoupper($txn->type) }}</span></td>
        <td class="dr">{{ $isDebit  ? number_format($txn->amount, 2) : '' }}</td>
        <td class="cr">{{ $isCredit ? number_format($txn->amount, 2) : '' }}</td>
        <td class="r" style="font-weight:600">{{ number_format($txn->balance_after, 2) }}</td>
      </tr>
      @endforeach
      <tr class="totals-row">
        <td colspan="4">Totals</td>
        <td class="r dr">{{ number_format($debits, 2) }}</td>
        <td class="r cr">{{ number_format($credits, 2) }}</td>
        <td class="r">{{ number_format($closeBal, 2) }}</td>
      </tr>
    </tbody>
  </table>
  @endif

  <div class="footer">
    <span>NABAAD Bank · Trust &bull; Security &bull; Progress — Confidential</span>
    <span>{{ $account->account_number }}</span>
    <span>{{ now()->format('d/m/Y H:i:s') }}</span>
  </div>

</div>
</body>
</html>
