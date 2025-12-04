<!-- Sidebar -->
<aside class="bg-[#14416b] text-white w-64 flex flex-col justify-between py-6 px-4">
    <div>
        <h2 class="text-2xl font-bold mb-8">Hotel Manager</h2>
        <nav>
            <ul class="space-y-2">
                <li>
                    <a href="/dashboard" class="flex items-center px-3 py-2 rounded {{ Request::is('dashboard') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                
                <li>
                    <a href="/reservations" class="flex items-center px-3 py-2 rounded {{ Request::is('reservations') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservations
                    </a>
                </li>
                <li>
                    <a href="/Guest-Checkin" class="flex items-center px-3 py-2 rounded {{ Request::is('checkin') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Guest Check in/out
                    </a>
                </li>
                <li>
                    <a href="/rooms" class="flex items-center px-3 py-2 rounded {{ Request::is('rooms') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Rooms
                    </a>
                </li>
                <li>
                    <a href="/guests" class="flex items-center px-3 py-2 rounded {{ Request::is('guests') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Transactions
                    </a>
                </li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <li>
                    <a href="/employee" class="flex items-center px-3 py-2 rounded {{ Request::is('employee') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Employee
                    </a>
                </li>
                @endif
                <li>
                    <a href="/report" class="flex items-center px-3 py-2 rounded {{ Request::is('report') ? 'bg-[#1e3a5f] font-semibold' : 'hover:bg-[#1e3a5f]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </a>
                </li>
                
            </ul>
        </nav>
    </div>
</aside>
