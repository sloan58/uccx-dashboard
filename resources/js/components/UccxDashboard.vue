<template>
<div class="wrapper ">
    <div class="main-panel" style="width: calc(100% - 10px)">
        <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="#pablo">UCCX Dashboard</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
        </div>
      </nav>
        <div class="content">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats bg-dark text-white">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-tap-01" :style="styleLoggedIn"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Logged In</p>
                                        <p class="card-title">{{ currentLoggedIn }}</p>
                                        <p/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <div class="stats">
                            <i class="nc-icon nc-alert-circle-i"></i> Online
                        </div>
                    </div>
                </div>
                </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats bg-dark text-white">
                    <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="nc-icon nc-check-2" :style="styleReady"></i>
                        </div>
                        </div>
                        <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Ready</p>
                            <p class="card-title">{{ currentReady }}</p>
                            <p/>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="nc-icon nc-alert-circle-i"></i> Available for calls
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats bg-dark text-white">
                    <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="nc-icon nc-simple-remove" :style="styleNotReady"></i>
                        </div>
                        </div>
                        <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Not Ready</p>
                            <p class="card-title">{{ currentNotReady }}</p>
                            <p/>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="nc-icon nc-alert-circle-i"></i> Not available for calls
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats bg-dark text-white">
                    <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="nc-icon nc-headphones" :style="styleTalking"></i>
                        </div>
                        </div>
                        <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Talking</p>
                            <p class="card-title">{{ currentTalking }}</p>
                            <p/>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="nc-icon nc-alert-circle-i"></i> Active on a call
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card bg-dark text-white">
                        <div class="card-header ">
                        <h5 class="card-title text-center">UCCX Agent Statistics</h5>
                        <p class="card-category text-center">10 Minute Lookback</p>
                        </div>
                        <div class="card-body ">
                        <canvas id=uccxChart width="400" height="100"></canvas>
                        </div>
                        <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="nc-icon nc-alert-circle-i"></i> Updated 1 minute ago
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {

        data() {
            return {
                loggedIn: [],
                ready: [],
                notReady: [],
                talking: [],
                labels: [],
                colors: {
                    yellow: 'rgba(244,208,63,1)',
                    blue: 'rgba(25,181,254,1)',
                    green: 'rgba(46,204,113,1)',
                    red: 'rgba(241,90,34,1)'
                },
                chart: null
            }
        },

        mounted() {
            this.fetchUccxData()

            Echo.channel('uccx')
                .listen('UccxDataUpdated', (event) => {
                    this.updateChartData(event.data)
            });
        },

        computed: {
            currentLoggedIn: function() {
                return this.loggedIn.slice(-1)[0]
            },
            currentReady: function() {
                return this.ready.slice(-1)[0]
            },
            currentNotReady: function() {
                return this.notReady.slice(-1)[0]
            },
            currentTalking: function() {
                return this.talking.slice(-1)[0]
            },
            styleLoggedIn: function() {
                return "color:" + this.colors.yellow
            },
            styleReady: function() {
                return "color:" + this.colors.green
            },
            styleNotReady: function() {
                return "color:" + this.colors.red
            },
            styleTalking: function() {
                return "color:" + this.colors.blue
            },
        },

        methods: {
            
            fetchUccxData() {
                window.axios.get('uccxData')
                .then(response => {
                    this.updateChartData(response.data)
                })
            },

            updateChartData(data) {
                this.loggedIn = []
                this.ready = []
                this.notReady = []
                this.talking = []
                this.labels = []
                
                this.loggedIn = data.loggedIn
                this.ready = data.ready
                this.notReady = data.notReady
                this.talking = data.talking
                this.labels = data.labels

                this.buildChart()
            },

            buildChart() {
                
                if(this.chart) {
                    this.chart.destroy()
                }
                
                const uccxCanvas = document.getElementById("uccxChart");

                Chart.defaults.global.defaultFontFamily = "Lato";
                Chart.defaults.global.defaultFontSize = 18;
                Chart.defaults.global.defaultFontColor = "#FFFFFF";

                let loggedIn = {
                        label: "Logged In",
                        data: this.loggedIn,
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: this.colors.yellow,
                        borderColor: this.colors.yellow,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: this.colors.yellow,
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: this.colors.yellow,
                        pointHoverBorderColor: this.colors.yellow,
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        spanGaps: false,
                    };

                let ready = {
                        label: "Ready",
                        data: this.ready,
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: this.colors.green,
                        borderColor: this.colors.green,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: this.colors.green,
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: this.colors.green,
                        pointHoverBorderColor: this.colors.green,
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        spanGaps: false,
                    };
                let notReady = {
                        label: "Not Ready",
                        data: this.notReady,
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: this.colors.red,
                        borderColor: this.colors.red,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: this.colors.red,
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: this.colors.red,
                        pointHoverBorderColor: this.colors.red,
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        spanGaps: false,
                    };
                let talking = {
                        label: "Talking",
                        data: this.talking,
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: this.colors.blue,
                        borderColor: this.colors.blue,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: this.colors.blue,
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: this.colors.blue,
                        pointHoverBorderColor: this.colors.blue,
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        spanGaps: false,
                    };

                let uccxData = {
                    labels: this.labels,
                    datasets: [loggedIn, ready, notReady, talking]
                };

                var chartOptions = {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            fontColor: 'white'
                        }
                }
                };

                this.chart = new Chart(uccxCanvas, {
                    type: 'line',
                    data: uccxData,
                    options: chartOptions
                });
            }
        }
    }
</script>
