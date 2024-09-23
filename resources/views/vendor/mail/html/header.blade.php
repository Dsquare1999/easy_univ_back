@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'EasyUniv')
<img src="{{ asset('storage/logo_easy_univ_bleu.svg') }}" alt="Logo Easy Univ">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>