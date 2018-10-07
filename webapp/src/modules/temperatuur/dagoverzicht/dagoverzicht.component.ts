import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import nl from '@angular/common/locales/nl';
import { registerLocaleData } from '@angular/common';
import { TemperatuurService } from '../temperatuur.service';
import { Chart } from 'angular-highcharts';
import * as Highcharts from 'highcharts';
import { ITemperatuurPerPeriode } from '../temperatuurPerPeriode.model';

@Component({
  templateUrl: './dagoverzicht.component.html',
  styleUrls: ['./dagoverzicht.component.css']
})
export class DagOverzichtComponent implements OnInit {
  chart = new Chart({
    chart: {
      zoomType: 'x'
    },
    title: {
      text: 'Temperatuurverloop'
    },
    xAxis: {
      type: 'datetime'
    },
    yAxis: {
      min: 0,
      title: {
        text: 'Temperatuur'
      }
    },
    legend: {
      align: 'right',
      verticalAlign: 'top',
      backgroundColor: 'white',
      borderColor: '#CCC',
      borderWidth: 1,
      shadow: false
    },
    colors: ['red', 'green'],
    plotOptions: {
      
    },
    series: []
  });

  temperaturen: ITemperatuurPerPeriode[] = [];

  currentMonth: number = 8;

  currentYear: number = 2018;

  currentMonthYear: string = '8-2018';

  maanden: ITemperatuurPerPeriode[] = [];

  constructor(
    private temperatuurService: TemperatuurService) {
  }

  ngOnInit() {
    registerLocaleData(nl);

    this.chart.ref$.subscribe((chart) => {
      chart.showLoading();
    });

    this.loadMaanden();

    this.loadTemperaturen();

    this.loadChartData();
  }

  public changeMonth() {
    var split = this.currentMonthYear.split('-');
    this.currentMonth = parseInt(split[0]);
    this.currentYear = parseInt(split[1]);

    this.loadTemperaturen();

    this.loadChartData();
  }

  private loadChartData() {
    this.chart.ref$.subscribe((chart) => {
      while (chart.series.length > 0)
        chart.series[0].remove(true);

      chart.showLoading();
    });

    this.temperatuurService.getTemperatuurPerUur(this.currentYear, this.currentMonth).subscribe((temperaturen) => {
      var series = {
        "avg_temp_indoor": { 'name': 'Avg Temp Indoor', 'data': [] },
        "min_temp_indoor": { 'name': 'Min Temp Indoor', 'data': [], visible: false },
        "max_temp_indoor": { 'name': 'Max Temp Indoor', 'data': [], visible: false },
        "avg_temp_1": { 'name': 'Avg Temp 1', 'data': [] },
        "min_temp_1": { 'name': 'Min Temp 1', 'data': [], visible: false },
        "max_temp_1": { 'name': 'Max Temp 1', 'data': [], visible: false },
        "avg_temp_2": { 'name': 'Avg Temp 2', 'data': [] },
        "min_temp_2": { 'name': 'Min Temp 2', 'data': [], visible: false },
        "max_temp_2": { 'name': 'Max Temp 2', 'data': [], visible: false },
        "avg_temp_3": { 'name': 'Avg Temp 3', 'data': [] },
        "min_temp_3": { 'name': 'Min Temp 3', 'data': [], visible: false },
        "max_temp_3": { 'name': 'Max Temp 3', 'data': [], visible: false }
      };

      temperaturen.forEach(function (value: ITemperatuurPerPeriode) {
        var timestamp = new Date(value.jaar, value.maand - 1, value.dag, value.uur).getTime();
        series['avg_temp_indoor']['data'].push([timestamp, value.avg_temp_indoor]);
        series['min_temp_indoor']['data'].push([timestamp, value.min_temp_indoor]);
        series['max_temp_indoor']['data'].push([timestamp, value.max_temp_indoor]);
        series['avg_temp_1']['data'].push([timestamp, value.avg_temp_1]);
        series['min_temp_1']['data'].push([timestamp, value.min_temp_1]);
        series['max_temp_1']['data'].push([timestamp, value.max_temp_1]);
        series['avg_temp_2']['data'].push([timestamp, value.avg_temp_2]);
        series['min_temp_2']['data'].push([timestamp, value.min_temp_2]);
        series['max_temp_2']['data'].push([timestamp, value.max_temp_2]);
        series['avg_temp_3']['data'].push([timestamp, value.avg_temp_3]);
        series['min_temp_3']['data'].push([timestamp, value.min_temp_3]);
        series['max_temp_3']['data'].push([timestamp, value.max_temp_3]);
      });

      this.chart.ref$.subscribe((chart) => {
        chart.hideLoading();

        for (var serie in series) {
          chart.addSeries(series[serie]);
        }
      });
    });
  }

  private loadTemperaturen() {
    this.temperatuurService.getTemperatuurPerDag(this.currentYear, this.currentMonth).subscribe((temperaturen) => {
      this.temperaturen = temperaturen;
    });
  }

  private loadMaanden() {
    this.temperatuurService.getTemperatuurPerMaand().subscribe((temperaturen) => {
      this.maanden = temperaturen;
    });
  }
  
}
