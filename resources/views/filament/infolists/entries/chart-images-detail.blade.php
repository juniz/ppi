@php
    $record = $getRecord();
@endphp

<div class="space-y-4">
    @if($record->chart_infeksi_image || $record->chart_pemasangan_image)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($record->chart_infeksi_image)
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-blue-50 px-3 py-2 border-b border-gray-200">
                        <h4 class="font-medium text-blue-800 text-xs">Grafik Infeksi HAIs</h4>
                    </div>
                    <div class="p-3">
                        <img src="{{ asset('storage/' . $record->chart_infeksi_image) }}" 
                             alt="Grafik Infeksi HAIs" 
                             class="w-full h-auto rounded border cursor-pointer hover:shadow-md transition-shadow"
                             style="max-height: 200px; object-fit: contain;"
                             onclick="openImageModal('{{ asset('storage/' . $record->chart_infeksi_image) }}', 'Grafik Infeksi HAIs')">
                    </div>
                </div>
            @endif

            @if($record->chart_pemasangan_image)
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-green-50 px-3 py-2 border-b border-gray-200">
                        <h4 class="font-medium text-green-800 text-xs">Grafik Pemasangan Alat</h4>
                    </div>
                    <div class="p-3">
                        <img src="{{ asset('storage/' . $record->chart_pemasangan_image) }}" 
                             alt="Grafik Pemasangan Alat" 
                             class="w-full h-auto rounded border cursor-pointer hover:shadow-md transition-shadow"
                             style="max-height: 200px; object-fit: contain;"
                             onclick="openImageModal('{{ asset('storage/' . $record->chart_pemasangan_image) }}', 'Grafik Pemasangan Alat')">
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-6 text-gray-500">
            <div class="mb-3">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-sm">Grafik HAIs belum tersedia untuk analisa ini</p>
            <p class="text-xs text-gray-400 mt-1">Grafik akan tersedia untuk analisa yang dibuat setelah fitur ini diaktifkan</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function openImageModal(imageSrc, title) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
    modal.onclick = function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    };

    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'bg-white rounded-lg max-w-6xl max-h-full overflow-auto';
    
    // Create header
    const header = document.createElement('div');
    header.className = 'flex justify-between items-center p-4 border-b border-gray-200';
    
    // Create title
    const titleElement = document.createElement('h3');
    titleElement.textContent = title;
    titleElement.className = 'text-lg font-semibold text-gray-900';
    
    // Create close button
    const closeButton = document.createElement('button');
    closeButton.innerHTML = '×';
    closeButton.className = 'text-2xl font-bold text-gray-500 hover:text-gray-700 w-8 h-8 flex items-center justify-center';
    closeButton.onclick = function() {
        document.body.removeChild(modal);
    };

    // Create image container
    const imageContainer = document.createElement('div');
    imageContainer.className = 'p-4';
    
    // Create image
    const image = document.createElement('img');
    image.src = imageSrc;
    image.alt = title;
    image.className = 'max-w-full h-auto';

    // Assemble modal
    header.appendChild(titleElement);
    header.appendChild(closeButton);
    imageContainer.appendChild(image);
    modalContent.appendChild(header);
    modalContent.appendChild(imageContainer);
    modal.appendChild(modalContent);

    // Add to body
    document.body.appendChild(modal);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Restore body scroll when modal is closed
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.style.overflow = '';
        }
    });
    
    closeButton.addEventListener('click', function() {
        document.body.style.overflow = '';
    });
}
</script>
@endpush