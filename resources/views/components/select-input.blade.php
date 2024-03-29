@props([
    'disabled' => false,
    'options' => [],
    'selectedValue' => null
])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm']) !!}>
    <option value="">Choose an option</option>
    @foreach($options as $value)
        <option value="{{ $value['value'] }}" @selected($value['value'] == $selectedValue)>{{ $value['label'] }}</option>
    @endforeach
</select>
