@php
    $record = $getRecord();
@endphp

<div class="space-y-2">
    @if($record->chart_infeksi_image)
        <div class="text-center">
            <p class="text-xs text-gray-600 mb-1">Grafik Infeksi HAIs</p>
            <img src="{{ asset('storage/' . $record->chart_infeksi_image) }}" 
                 alt="Grafik Infeksi HAIs" 
                 class="max-w-full h-auto rounded border cursor-pointer hover:shadow-lg transition-shadow"
                 style="max-height: 150px;"
                 onclick="openImageModal('{{ asset('storage/' . $record->chart_infeksi_image) }}', 'Grafik Infeksi HAIs')">
        </div>
    @endif

    @if($record->chart_pemasangan_image)
        <div class="text-center">
            <p class="text-xs text-gray-600 mb-1">Grafik Pemasangan Alat</p>
            <img src="{{ asset('storage/' . $record->chart_pemasangan_image) }}" 
                 alt="Grafik Pemasangan Alat" 
                 class="max-w-full h-auto rounded border cursor-pointer hover:shadow-lg transition-shadow"
                 style="max-height: 150px;"
                 onclick="openImageModal('{{ asset('storage/' . $record->chart_pemasangan_image) }}', 'Grafik Pemasangan Alat')">
        </div>
    @endif

    @if(!$record->chart_infeksi_image && !$record->chart_pemasangan_image)
        <div class="text-center text-gray-500 text-xs">
            <p>Grafik belum tersedia</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function openImageModal(imageSrc, title) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.onclick = function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    };

    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'bg-white rounded-lg p-4 max-w-4xl max-h-full overflow-auto';
    
    // Create close button
    const closeButton = document.createElement('button');
    closeButton.innerHTML = '×';
    closeButton.className = 'float-right text-2xl font-bold text-gray-500 hover:text-gray-700';
    closeButton.onclick = function() {
        document.body.removeChild(modal);
    };

    // Create title
    const titleElement = document.createElement('h3');
    titleElement.textContent = title;
    titleElement.className = 'text-lg font-semibold mb-4 clear-both';

    // Create image
    const image = document.createElement('img');
    image.src = imageSrc;
    image.alt = title;
    image.className = 'max-w-full h-auto';

    // Assemble modal
    modalContent.appendChild(closeButton);
    modalContent.appendChild(titleElement);
    modalContent.appendChild(image);
    modal.appendChild(modalContent);

    // Add to body
    document.body.appendChild(modal);
}
</script>
@endpush