@php
    $selectedValue = (string) ($value ?? '');
    $selectedValue = $selectedValue !== '' ? substr($selectedValue, 0, 5) : '';
    $selectedOption = collect($timeOptions)->firstWhere('value', $selectedValue);
    $triggerText = $selectedOption['label'] ?? ($allowNoBreak && $selectedValue === '' ? 'No break time' : $placeholder);
@endphp

<div class="time-picker" data-time-picker data-field="{{ $name }}" data-allow-no-break="{{ $allowNoBreak ? 'true' : 'false' }}">
    <label for="{{ $name }}_trigger" class="{{ $labelClass }}">{{ $label }}</label>
    <button
        type="button"
        id="{{ $name }}_trigger"
        class="time-picker-trigger"
        data-placeholder="{{ $placeholder }}"
        aria-expanded="false"
        aria-controls="{{ $name }}_panel"
    >
        <span class="time-picker-trigger-text">{{ $triggerText }}</span>
        <i class="fas fa-clock" aria-hidden="true"></i>
    </button>

    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $selectedValue }}">

    <div class="time-picker-panel" id="{{ $name }}_panel">
        <div class="time-card-grid">
            @if($allowNoBreak)
                <button
                    type="button"
                    class="time-card no-break"
                    data-value=""
                    data-display="No break time"
                    data-minutes=""
                >
                    No break time
                </button>
            @endif

            @foreach($timeOptions as $timeOption)
                @php
                    [$hour, $minute] = array_map('intval', explode(':', $timeOption['value']));
                    $minutes = ($hour * 60) + $minute;
                @endphp
                <button
                    type="button"
                    class="time-card"
                    data-value="{{ $timeOption['value'] }}"
                    data-display="{{ $timeOption['label'] }}"
                    data-minutes="{{ $minutes }}"
                >
                    {{ $timeOption['label'] }}
                </button>
            @endforeach
        </div>
        <div class="time-picker-hint" id="{{ $name }}_hint" aria-live="polite"></div>
    </div>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
