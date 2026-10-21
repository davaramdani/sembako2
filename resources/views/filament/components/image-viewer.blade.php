<div>
    @if ($getState())
        <a href="{{ asset('storage/' . $getState()) }}" target="_blank">
            <img src="{{ asset('storage/' . $getState()) }}" alt="Bukti Pembayaran" style="max-width: 300px; height: auto; border-radius: 5px;">
        </a>
    @else
        <p class="text-gray-500">Tidak ada bukti pembayaran yang diunggah.</p>
    @endif
</div>