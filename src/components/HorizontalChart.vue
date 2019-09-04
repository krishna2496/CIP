<template>
    <div>
        <canvas v-show='data' ref="themeChartRefs"></canvas>
        <div v-show="!data">
            <h5>Loading...</h5>
        </div>
    </div>
</template>
<script>
export default {
    name: 'horizontal-chart',
    props: {
        labels: {
            type: Array,
            default: []
        },
        data: {
            type: Array,
            default: []
        },
        fontColor: {
            type: String,
            default: "#414141"
        },
        backgroundColor: {
            type: String,
            default: "#dc3545"
        },
        borderColor: {
            type: String,
            default: "#dc3545"
        }
    },
    mounted() {
        var themeChartRefs = this.$refs.themeChartRefs;
        var themeContent = themeChartRefs.getContext("2d");
        var themeChart = new Chart(themeContent, {
        type: "horizontalBar",
        data: {
            labels: this.labels,
            datasets: [
            {
                data: this.data,
                backgroundColor: this.backgroundColor,
                borderColor: this.borderColor,
                borderWidth: 1
            }
            ]
        },
        options: {
            legend: {
            display: false
            },
            scales: {
            yAxes: [
                {
                ticks: {
                    beginAtZero: true
                }
                }
            ],
            xAxes: [
                {
                ticks: {
                    fontColor: this.fontColor,
                    fontSize: 14,
                    max: Math.round(Math.max(...this.data)*2),
                    min: 0,
                    stepSize: Math.round((Math.max(...this.data)*2)/10)
                }
                }
            ]
            }
        }
        });
    }
}
</script>