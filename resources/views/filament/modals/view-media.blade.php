
@php
    $mediaUrl = $media?->getUrl();
    $isImage = $media && str_starts_with($media->mime_type, 'image/');
@endphp

<div class="flex justify-center p-4">
    @if ($mediaUrl)
        @if ($isImage)
            <img src="{{ $mediaUrl }}" alt="Pratinjau Dokumen" class="max-w-full h-auto rounded-lg shadow-md">
        @else
            <div class="text-center">
                <p class="mb-4 text-gray-600 dark:text-gray-300">Pratinjau tidak tersedia untuk tipe file ini.</p>
                <a href="{{ $mediaUrl }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                    Lihat/Unduh File ({{ $media->file_name }})
                </a>
            </div>
        @endif
    @else
        <p class="text-gray-500">Media tidak ditemukan.</p>
    @endif
</div>
