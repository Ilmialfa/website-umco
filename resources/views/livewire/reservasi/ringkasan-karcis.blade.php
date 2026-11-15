{{-- DESKTOP (>=1024px): Kanan 5 kol Karcis sticky yang terisi live --}}
<aside class="hidden lg:block lg:col-span-5 lg:sticky lg:top-24">
    <x-umco.karcis
        title="Karcis meja"
        code="No. ......"
    >
        <x-umco.karcis-line label="Hari" :value="$dateDisplay" />
        <x-umco.karcis-line label="Jam" :value="$timeDisplay" />
        <x-umco.karcis-line label="Orang" :value="$partySize . ' orang'" />
        <x-umco.karcis-line label="Tempat" :value="$seatingDisplay" />
        <x-umco.karcis-line label="Nama" :value="$name !== '' ? $name : '......'" />
        <x-umco.karcis-line label="Titipan" :value="$itemsSummaryText" />
        <x-umco.karcis-line label="Total" :value="\App\Support\IndonesianFormat::rupiah($quoteData['subtotal'])" />
        <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($quoteData['amount_due'])" />
    </x-umco.karcis>
</aside>
