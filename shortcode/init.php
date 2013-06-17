<?php
if(!class_exists('RJ_Quickcharts_Shortcode'))
{
    class RJ_Quickcharts_Shortcode
    {

        public function __construct()
        {
            add_action('init', array(&$this, 'init'));
            add_shortcode('show-rjqc', array($this, 'show_rjqc_function'));
        }

        public function init()
        {
        }

        public function show_rjqc_function($atts) {
            extract(shortcode_atts(array(
                'id' => 1,
            ), $atts));

            $return_string = '';
            global $wpdb;
            global $table_name;

            $sql = "SELECT * FROM $table_name WHERE id = $id";
            $chart = $wpdb->get_results($sql);

            if ($chart) {
                $id         = $chart[0]->id;
                $created    = $chart[0]->created;
                $type       = $chart[0]->type;
                $title      = $chart[0]->title;
                $subtitle   = $chart[0]->subtitle;
                $tooltipSuffix = $chart[0]->tooltipSuffix;
                $yAxisCats  = $chart[0]->xAxisCats;
                $yAxisText  = $chart[0]->yAxisTitleText;
                $legend     = $chart[0]->legendOn;
                $legend     = ($legend == 1) ? 'true' : 'false';
                $series     = $chart[0]->series;
                $hotSeries  = $chart[0]->hotSeries;
                $chartFill  = false;

                $return_string .= "<div id='rjqc_container_$id'></div>";
                $return_string .= "<script>";
                $return_string .= "(function ($) {

                    tooltipSuffix = '$tooltipSuffix';
                    yAC = $yAxisCats;
                    yAxisCats = [];
                    for (i=0;i<yAC.length;i++) {
                        yAxisCats.push({label:yAC[i]});
                    }

                ";

                $return_string .= "switch ('$type') {
                                        case 'bar':
                                            r = jQuery.jqplot.BarRenderer;
                                            break;
                                        case 'pie':
                                            r = jQuery.jqplot.PieRenderer;
                                            break;
                                        case 'donut':
                                            r = jQuery.jqplot.DonutRenderer;
                                            break;
                                        default:
                                            r = jQuery.jqplot.LineRenderer;
                                            break;
                                    }";

                $return_string .= "chart = jQuery.jqplot('rjqc_container_$id', $series, {
                                    seriesDefaults:{
                                        fill: '$chartFill',
                                        renderer: r
                                    },
                                    title: {
                                        text: '$title',
                                        fontSize: '20px',
                                        fontFamily: 'Arial',
                                        textColor: '#666',
                                    },
                                    grid: {
                                        backgroundColor: 'white',
                                        borderWidth: 0,
                                        gridLineColor: '#ccc',
                                        gridLineWidth: 1.01,
                                        borderColor: '#ccc',
                                        shadow: false
                                    },
                                    highlighter: {
                                        show: true,
                                        tooltipLocation: 'n',
                                        tooltipAxes: 'y',
                                        formatString: '%s',
                                        showMarker: false,
                                        //bringSeriesToFront: true,
                                        showTooltip: true,
                                        fadeTooltip: false,
                                        tooltipFadeSpeed: '1'
                                    },
                                    legend: {
                                        margin: 0,
                                        padding: 0,
                                        background: 'transparent',
                                        textColor: '#666',
                                        fontFamily: 'Arial, Verdana, Helvetica',
                                        fontSize: '11px',
                                        border: 'none',
                                        show: true,
                                        location: 'n',
                                        showSwatch: true,
                                        placement: 'outsideGrid',
                                        renderer: jQuery.jqplot.EnhancedLegendRenderer,
                                        rendererOptions: {
                                            numberRows: 1,
                                            disableIEFading: true
                                        }
                                    },
                                    cursor: {
                                        show: false
                                    },
                                    series: yAxisCats,
                                    seriesColors:['#40C0CB', '#AEE239', '#CC333F', '#EB6841', '#2A363B','#F9D423','#00DFFC','#FF847C','#F9F2E7','#E84A5F'],
                                    axesDefaults: {
                                        tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer,
                                        tickOptions: {
                                            angle: 0,
                                            fontSize: '11px',
                                            fontWeight: 'normal',
                                            _styles: {
                                                'padding': '4px 0px 8px',
                                                'font-weight': 'light'
                                            }
                                        }
                                    },
                                    axesStyles: {
                                        borderWidth: 0,
                                        label: {
                                           fontFamily: 'Arial',
                                           textColor: '#666'
                                        }
                                    },
                                    axes: {
                                        xaxis: {
                                            label: '',
                                            renderer: jQuery.jqplot.CategoryAxisRenderer,
                                            labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer,
                                            tickOptions: {
                                                mark: false
                                            },
                                            labelOptions: {
                                                show: true,
                                                fontSize: '14px'
                                            }
                                        },
                                        yaxis:{
                                            renderer: jQuery.jqplot.LogAxisRenderer,
                                            tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer,
                                            labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer,
                                            label: '$yAxisText',
                                            labelOptions: {
                                                show: true,
                                                fontSize: '14px'
                                            },
                                            tickOptions: {
                                                mark: false,
                                                labelPosition: 'middle',
                                                angle: -90
                                            }
                                        }
                                    }
                                });

                                // Handle legend hiding on the client
                                if($legend === false) {
                                    jQuery('.jqplot-table-legend').hide();
                                } else {
                                    jQuery('.jqplot-table-legend').show();
                                }

                                jQuery('#rjqc_container_$id').bind('jqplotDataMouseOver', function (ev, seriesIndex, pointIndex, data) {
                                    jQuery('.jqplot-highlighter-tooltip').html('' + data[1] + tooltipSuffix);
                                });
                                ";

                $return_string .= "})(jQuery);";
                $return_string .= "</script>";

                return $return_string;
            }
        }

    }
}
