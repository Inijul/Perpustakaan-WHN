@props([
    'buttonText' => 'Submit',
    'loadingText' => 'Loading...',
    'isLoading' => false,
    'buttonClass' => 'flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96] flex items-center justify-center transition-all duration-300',
    'disabledClass' => 'opacity-50 cursor-not-allowed',
    'spinnerType' => 'svg' // 'svg' or 'css'
])

<button type="submit" 
        class="loading-button {{ $buttonClass }}"
        :disabled="isLoading"
        :class="{ '{{ $disabledClass }}': isLoading }">
    
    <!-- Button Text -->
    <span x-show="!isLoading" x-text="buttonText">{{ $buttonText }}</span>
    
    <!-- Loading Text -->
    <span x-show="isLoading" x-text="loadingText">{{ $loadingText }}</span>
    
    <!-- Loading Spinner -->
    @if($spinnerType === 'svg')
        <svg x-show="isLoading" 
             class="loading-spinner-svg ml-2 text-white"
             xmlns="http://www.w3.org/2000/svg" 
             fill="none" 
             viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @else
        <div x-show="isLoading" class="loading-spinner ml-2"></div>
    @endif
</button>
