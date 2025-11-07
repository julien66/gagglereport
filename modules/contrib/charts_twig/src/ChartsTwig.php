<?php

namespace Drupal\charts_twig;

use Drupal\Component\Utility\Xss;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ChartsTwig Extension.
 *
 * @package Drupal\charts_twig
 */
class ChartsTwig extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('chart', $this->createChart(...)),
    ];
  }

  /**
   * Returns a chart given the required parameters. See README for details.
   *
   * @param string $id
   *   The ID of the chart.
   * @param string $chart_type
   *   The type of chart to be rendered.
   * @param string $title
   *   The title of the chart.
   * @param array $chart_data
   *   The data to be rendered in the chart.
   * @param array $xaxis
   *   The x-axis data.
   * @param array $yaxis
   *   The y-axis data.
   * @param array $options
   *   The options for the chart.
   *
   * @return array
   *   The chart.
   */
  public function createChart(string $id, string $chart_type, string $title, array $chart_data, array $xaxis, array $yaxis, array $options): array {

    $chart = [];
    $chart[$id] = [
      '#type' => 'chart',
      '#chart_type' => $chart_type,
      '#title' => $title ? Xss::filter($title) : '',
      '#raw_options' => $options,
    ];
    foreach ($chart_data as $key => $chart_datum) {
      $chart[$id]['series_' . $key] = [
        '#type' => 'chart_data',
        '#title' => !empty($chart_datum['title']) ? Xss::filter($chart_datum['title']) : '',
        '#data' => $chart_datum['data'] ?? [],
      ];
      if (!empty($chart_datum['color'])) {
        $chart[$id]['series_' . $key]['#color'] = $chart_datum['color'];
      }
    }
    $chart[$id]['xaxis'] = [
      '#type' => 'chart_xaxis',
      '#title' => !empty($xaxis['title']) ? Xss::filter($xaxis['title']) : '',
      '#labels' => $xaxis['labels'] ?? [],
    ];
    $chart[$id]['yaxis'] = [
      '#type' => 'chart_yaxis',
      '#title' => !empty($yaxis['title']) ? Xss::filter($yaxis['title']) : '',
    ];

    return $chart;
  }

}
