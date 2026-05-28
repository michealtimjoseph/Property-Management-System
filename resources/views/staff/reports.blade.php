<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-10 bg-[#F1F5F9] min-h-screen font-sans antialiased selection:bg-[#853953]/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Modern Segmented Header Block --}}
            <div class="bg-white rounded-3xl p-6 mb-8 border border-slate-200/50 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 h-full w-2 bg-[#853953]"></div>
                <div>
                    <span class="text-[11px] font-extrabold text-[#853953] uppercase tracking-widest bg-[#853953]/5 px-3 py-1 rounded-full">Management Console</span>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-2">Analytics & Reports</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">Compile and review core operational logs, revenue frameworks, and leasing metrics.</p>
                </div>
            </div>


            {{-- Main Layout Restructure --}}
            <div class="space-y-8">
                
                {{-- Dynamic, Premium Card Rows Matrix --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($reports as $report)
                    <a href="{{ route('staff.reports.generate', ['type' => $report['slug']]) }}" 
                       class="group block bg-white rounded-3xl border border-slate-200/70 overflow-hidden hover:border-[#853953]/40 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#853953] focus:ring-offset-2">
                        
                        <div class="p-6 sm:p-8 flex flex-col justify-between h-full min-h-[220px]">
                            
                            {{-- Top Segment --}}
                            <div class="flex items-start gap-5">
                                {{-- Brand-New Custom Vector Iconography Mapping --}}
                                <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-700 group-hover:bg-[#853953] group-hover:text-white group-hover:border-transparent group-hover:scale-105 transition-all duration-300 flex-shrink-0 shadow-inner">
                                    @if(($report['slug'] ?? '') === 'lease-summary' || str_contains($report['slug'] ?? '', 'lease'))
                                        <!-- Interactive Signature Lease Icon -->
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11a13.916 13.916 0 00-1.743-7.442l-.055-.094m3.44 2.04A13.916 13.916 0 0111 11c0 2.133-.48 4.156-1.343 5.971m2.342-12.042a14.07 14.07 0 013.232 4.414m5.244.428a13.957 13.957 0 01-3.232 4.414m2.133-7.53A14.07 14.07 0 0118 11c0 4.757-2.37 8.959-6 11.58M12 2a13.974 13.974 0 00-3.461 4.63" />
                                        </svg>
                                    @elseif(($report['slug'] ?? '') === 'revenue-report' || str_contains($report['slug'] ?? '', 'revenue') || str_contains($report['slug'] ?? '', 'payment'))
                                        <!-- Solid Financial Trend Curve Icon -->
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M3 12h18M5 5l14 14M5 19L14 10m2-2l3-3" />
                                        </svg>
                                    @elseif(str_contains($report['slug'] ?? '', 'user') || str_contains($report['slug'] ?? '', 'tenant'))
                                        <!-- Secure Account Key Management Icon -->
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    @else
                                        <!-- Database Engine Stack Icon -->
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-[#853953] transition-colors duration-200">
                                        {{ $report['title'] }}
                                    </h3>
                                    <p class="text-sm text-slate-500 font-medium leading-relaxed pr-2">
                                        {{ $report['desc'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Bottom Segment --}}
                            <div class="flex items-center justify-between pt-6 mt-6 border-t border-slate-100">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400 group-hover:text-[#853953] transition-colors">
                                    <span>Download PDF</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>