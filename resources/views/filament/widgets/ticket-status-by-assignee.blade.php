<x-filament::widget>
    <x-filament::card>
        
        <div class="flex items-center justify-between gap-8 py-2">

            <h2 class="text-xl font-semibold tracking-tight filament-card-heading">
                Ticket Status By Assignee Person
            </h2>

            <div class="relative h-10">

                <div class="flex items-center justify-between gap-4">

                </div>
            </div>

        </div>
        <div aria-hidden="true" class="filament-hr border-t py-2"></div>

        <div id="stacked-bar"></div>

        <script>
            const assigneeSeries = @json($series);
            const categories = @json($categories);

            var options = {
                series: assigneeSeries,
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    stackType: '100%'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    },
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: categories,
                },
                tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
                },
                fill: {
                    opacity: 1            
                },
            };

            var chart = new ApexCharts(document.querySelector("#stacked-bar"), options);

            chart.render();
        </script>
    </x-filament::card>
</x-filament::widget>