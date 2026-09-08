@php
    $user = $node['user'];
    $type = $node['type'] ?? 'member';
    $line = $node['line'] ?? null;
    $children = $node['children'] ?? [];
    $isOwner = $type === 'owner';
    $isUpline = $type === 'upline';
@endphp

<div class="flex flex-col items-center">

    <div
        class="w-48 rounded-xl border p-3 shadow-sm transition hover:shadow-md
        {{ $isOwner
            ? 'border-red-200 bg-red-50'
            : ($isUpline
                ? 'border-slate-200 bg-slate-50'
                : 'border-slate-200 bg-white') }}">

        <div class="flex items-center gap-2.5">

            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                {{ $isOwner ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600' }}">

                <span class="text-xs font-bold">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </span>

            </div>

            <div class="min-w-0">

                <p class="truncate text-xs font-semibold text-slate-900">
                    {{ $user->name }}
                </p>

                <p class="truncate text-[9px] text-slate-500">
                    {{ $user->member_id }}
                </p>

            </div>

        </div>

        <div class="mt-2">

            @if ($isOwner)
                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[9px] font-semibold text-red-700">
                    Owner Group
                </span>
            @elseif ($isUpline)
                <span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[9px] font-semibold text-slate-600">
                    Upline
                </span>
            @else
                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-semibold text-slate-600">
                    Line {{ $line }}
                </span>
            @endif

        </div>

        @if (!$isOwner && !$isUpline && isset($node['recruiter']) && $node['recruiter'])
            <p class="mt-1.5 truncate text-[9px] text-slate-400">
                Direkrut oleh
                <span class="font-semibold text-slate-600">
                    {{ $node['recruiter']->name }}
                </span>
            </p>
        @endif

    </div>

    @if (count($children))

        <div class="h-5 w-px bg-slate-300"></div>

        <div class="relative flex items-start gap-3">

            @if (count($children) > 1)
                <div class="absolute left-0 right-0 top-0 h-px bg-slate-300"></div>
            @endif

            @foreach ($children as $child)
                <div class="relative flex flex-col items-center">

                    <div class="h-5 w-px bg-slate-300"></div>

                    @include('aganda.structure.node', [
                        'node' => $child,
                    ])

                </div>
            @endforeach

        </div>

    @endif

</div>
