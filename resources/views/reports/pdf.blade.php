<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportName }} - {{ config('app.name', 'Hotel Management') }}</title>
    <style>
        /* Professional PDF Styling */
        @page {
            margin: 50px 25px;
            font-family: DejaVu Sans, sans-serif;
        }
        
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
        }
        
        /* Use Unicode escape for PHP Peso symbol to ensure it works in PDF */
        .php-currency:before {
            content: "\20B1";
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
        }
        
        .hotel-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 22px;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .report-meta {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            max-width: 150px;
            max-height: 80px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            background-color: #ecf0f1;
            padding: 10px 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
        }
        
        .table-container {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table-container th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        
        .table-container td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        
        .table-container tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }
        
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        
        .summary-value {
            font-weight: bold;
            color: #3498db;
        }
        
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-number:after {
            content: "Page " counter(page);
        }
        
        /* Metrics styling */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .metric-card {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .metric-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0;
        }
        
        .metric-trend {
            font-size: 11px;
            font-weight: bold;
        }
        
        .trend-up {
            color: #27ae60;
        }
        
        .trend-down {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        @if(isset($logoPath) && file_exists(public_path($logoPath)))
            <div class="logo">
                <img src="{{ public_path($logoPath) }}" alt="Hotel Logo">
            </div>
        @endif
        
        <div class="hotel-name">{{ config('app.name', 'Grand Hotel & Resort') }}</div>
        <div class="report-title">{{ $reportName }}</div>
        <div class="report-meta">
            Generated on: {{ date('F d, Y h:i A') }} | 
            Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
        </div>
    </div>

    <!-- Summary Metrics Section -->
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">&#8369;{{ number_format($reportData['totalRevenue'] ?? 0, 2) }}</div>
                @if(isset($reportData['revenueGrowth']) && $reportData['revenueGrowth'] > 0)
                    <div class="metric-trend trend-up">+{{ $reportData['revenueGrowth'] }}% vs previous period</div>
                @endif
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Average Occupancy</div>
                <div class="metric-value">{{ $reportData['avgOccupancy'] ?? 0 }}%</div>
                @if(isset($reportData['occupancyGrowth']) && $reportData['occupancyGrowth'] > 0)
                    <div class="metric-trend trend-up">+{{ $reportData['occupancyGrowth'] }}% vs previous period</div>
                @endif
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Total Bookings</div>
                <div class="metric-value">{{ $reportData['totalBookings'] ?? 0 }}</div>
                <div class="metric-trend">{{ $reportData['completedBookings'] ?? 0 }} completed</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Avg Daily Rate (ADR)</div>
                <div class="metric-value">&#8369;{{ number_format($reportData['avgDailyRate'] ?? 0, 2) }}</div>
                @if(isset($reportData['adrGrowth']) && $reportData['adrGrowth'] > 0)
                    <div class="metric-trend trend-up">+{{ $reportData['adrGrowth'] }}% growth</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Revenue Report -->
    @if($reportType == 'revenue')
    <div class="section">
        <div class="section-title">Revenue Details</div>
        
        <!-- Revenue Summary -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Room Revenue:</span>
                <span class="summary-value">&#8369;{{ number_format($reportData['roomRevenue'] ?? 0, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Other Revenue:</span>
                <span class="summary-value">&#8369;{{ number_format($reportData['otherRevenue'] ?? 0, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Revenue:</span>
                <span class="summary-value highlight">&#8369;{{ number_format($reportData['totalRevenue'] ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Daily Revenue Table -->
        @if(isset($reportData['dailyRevenue']) && count($reportData['dailyRevenue']) > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Room Revenue</th>
                        <th>Other Revenue</th>
                        <th>Total Revenue</th>
                        <th>Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['dailyRevenue'] as $daily)
                    <tr>
                        <td>{{ $daily['date']->format('M d, Y') }}</td>
                        <td>&#8369;{{ number_format($daily['room_revenue'], 2) }}</td>
                        <td>&#8369;{{ number_format($daily['other_revenue'], 2) }}</td>
                        <td>&#8369;{{ number_format($daily['total_revenue'], 2) }}</td>
                        <td>{{ $daily['bookings'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Totals</th>
                        <th>&#8369;{{ number_format(collect($reportData['dailyRevenue'])->sum('room_revenue'), 2) }}</th>
                        <th>&#8369;{{ number_format(collect($reportData['dailyRevenue'])->sum('other_revenue'), 2) }}</th>
                        <th>&#8369;{{ number_format(collect($reportData['dailyRevenue'])->sum('total_revenue'), 2) }}</th>
                        <th>{{ collect($reportData['dailyRevenue'])->sum('bookings') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        
    </div>
    @endif

    <!-- Occupancy Report -->
    @if($reportType == 'occupancy')
    <div class="section">
        <div class="section-title">Occupancy Analysis</div>
        
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Available Rooms:</span>
                <span class="summary-value">{{ $reportData['totalRooms'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Avg Daily Occupied Rooms:</span>
                <span class="summary-value">{{ $reportData['avgOccupiedRooms'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Peak Occupancy Date:</span>
                <span class="summary-value">{{ $reportData['peakDate'] ?? 'N/A' }} ({{ $reportData['peakOccupancy'] ?? 0 }}%)</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Lowest Occupancy Date:</span>
                <span class="summary-value">{{ $reportData['lowDate'] ?? 'N/A' }} ({{ $reportData['lowOccupancy'] ?? 0 }}%)</span>
            </div>
        </div>

        <!-- Daily Occupancy Table -->
        @if(isset($reportData['dailyOccupancy']) && count($reportData['dailyOccupancy']) > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Available</th>
                        <th>Occupied</th>
                        <th>Occupancy %</th>
                        <th>ADR</th>
                        <th>RevPAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['dailyOccupancy'] as $daily)
                    <tr>
                        <td>{{ $daily['date']->format('M d, Y') }}</td>
                        <td>{{ $daily['available_rooms'] }}</td>
                        <td>{{ $daily['occupied_rooms'] }}</td>
                        <td>{{ number_format($daily['occupancy_rate'], 1) }}%</td>
                        <td>&#8369;{{ number_format($daily['adr'], 2) }}</td>
                        <td>&#8369;{{ number_format($daily['revpar'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    <!-- Guest Report -->
    @if($reportType == 'guest')
    <div class="section">
        <div class="section-title">Guest Analysis</div>
        
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Guests:</span>
                <span class="summary-value">{{ $reportData['totalGuests'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">New Guests:</span>
                <span class="summary-value">{{ $reportData['newGuests'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Returning Guests:</span>
                <span class="summary-value">{{ $reportData['returningGuests'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Avg Length of Stay:</span>
                <span class="summary-value">{{ $reportData['avgLengthOfStay'] ?? 0 }} nights</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Walk-in Guests:</span>
                <span class="summary-value">{{ $reportData['guestTypes']['walk_in'] ?? 0 }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Advance Booking Guests:</span>
                <span class="summary-value">{{ $reportData['guestTypes']['advance'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Top Guests -->
        @if(isset($reportData['topGuests']) && count($reportData['topGuests']) > 0)
        <div class="table-container">
            <div class="section-title" style="margin-top: 30px;">Top Guests by Revenue</div>
            <table>
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Email</th>
                        <th>Stays</th>
                        <th>Total Nights</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['topGuests'] as $guest)
                    <tr>
                        <td>{{ $guest['first_name'] }} {{ $guest['last_name'] }}</td>
                        <td>{{ $guest['email'] }}</td>
                        <td>{{ $guest['stay_count'] }}</td>
                        <td>{{ $guest['total_nights'] }}</td>
                        <td>&#8369;{{ number_format($guest['total_revenue'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    

    <!-- Insights and Recommendations -->
    <div class="section">
        <div class="section-title">Insights & Recommendations</div>
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db;">
            <p style="margin-bottom: 10px;"><strong>Performance Summary:</strong></p>
            <p style="margin-bottom: 15px;">
                @if($reportType == 'revenue')
                    Total revenue for the period was &#8369;{{ number_format($reportData['totalRevenue'] ?? 0, 2) }}. 
                    @if(isset($reportData['revenueGrowth']) && $reportData['revenueGrowth'] > 0)
                        This represents a {{ $reportData['revenueGrowth'] }}% increase compared to the previous period.
                    @endif
                @elseif($reportType == 'occupancy')
                    Average occupancy for the period was {{ $reportData['avgOccupancy'] ?? 0 }}%. 
                    @if(isset($reportData['occupancyGrowth']) && $reportData['occupancyGrowth'] > 0)
                        Occupancy increased by {{ $reportData['occupancyGrowth'] }}% compared to the previous period.
                    @endif
                @elseif($reportType == 'guest')
                    The hotel hosted {{ $reportData['totalGuests'] ?? 0 }} guests during this period, 
                    with {{ $reportData['returningGuests'] ?? 0 }} returning guests representing 
                    {{ $reportData['totalGuests'] > 0 ? number_format(($reportData['returningGuests'] / $reportData['totalGuests']) * 100, 1) : 0 }}% of total guests.
                    Walk-in guests: {{ $reportData['guestTypes']['walk_in'] ?? 0 }}, Advance bookings: {{ $reportData['guestTypes']['advance'] ?? 0 }}.
                @endif
            </p>
            
            <p style="margin-bottom: 10px;"><strong>Recommendations:</strong></p>
            <ul style="margin-left: 20px; margin-bottom: 15px;">
                @if($reportType == 'revenue')
                    <li>Focus on upselling room upgrades and additional services to increase revenue per guest</li>
                    <li>Implement dynamic pricing strategies during high-demand periods</li>
                    <li>Promote package deals to increase average daily rate</li>
                @endif
                @if($reportType == 'occupancy')
                    <li>Target marketing campaigns during low-occupancy periods</li>
                    <li>Offer last-minute deals to fill unsold rooms</li>
                    <li>Consider partnerships with local businesses for corporate rates</li>
                @endif
                @if($reportType == 'guest')
                    <li>Implement a loyalty program to encourage repeat business</li>
                    <li>Personalize guest experience based on previous stays</li>
                    <li>Collect and act on guest feedback to improve satisfaction</li>
                @endif
            </ul>
            
            <p style="font-style: italic; color: #6c757d;">
                Report generated by {{ config('app.name', 'Hotel Management System') }} on {{ date('F d, Y') }}
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Confidential Report - {{ config('app.name', 'Grand Hotel & Resort') }}</div>
        <div>Generated on {{ date('F d, Y h:i A') }} | Page <span class="page-number"></span></div>
        <div style="margin-top: 5px; font-size: 9px;">
            This report is confidential and intended only for authorized personnel. Unauthorized distribution is prohibited.
        </div>
    </div>
</body>
</html>