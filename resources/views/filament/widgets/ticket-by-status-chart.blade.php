<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between gap-8 py-2">

            <h2 class="text-xl font-semibold tracking-tight filament-card-heading">
                Ticket Status
            </h2>

            <div class="relative h-10">

                <div class="flex items-center justify-between gap-4">

                </div>
            </div>

        </div>
        <div aria-hidden="true" class="filament-hr border-t py-2"></div>

        <div id="pie-chart"></div>

        <script>
            const statusSeries = @json($series);
            const labels = @json($labels);
            const colors = @json($colors);
            const ticketCounts = @json($ticketCounts);

            var options = {
                series: statusSeries,
                chart: {
                    type: 'pie',
                    height: 350,
                },
                labels: labels,
                colors: colors,
                legend: {
                    labels: {
                        colors: '#9ca3af',
                        fontWeight: 600
                    }
                },
                plotOptions: {
                    pie: {
                        size: ticketCounts
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Math.round((val/100) * ticketCounts)
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#pie-chart"), options);

            chart.render();
        </script>
    </x-filament::card>
</x-filament::widget>
