<h2>Pengajuan Reimbursement Baru</h2>

<p><strong>Judul:</strong> {{ $reimbursement->title }}</p>
<p><strong>Jumlah:</strong> Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}</p>
<p><strong>Pengaju:</strong> {{ $reimbursement->user->name }}</p>
<p>Silakan login ke sistem untuk memproses reimbursement ini.</p>
