@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} rows="4" {!! $attributes->merge(['class' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm']) !!}></textarea>
