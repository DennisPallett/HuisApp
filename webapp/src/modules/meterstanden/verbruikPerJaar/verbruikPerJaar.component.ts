import { Component, OnInit } from '@angular/core';
import { Chart } from 'angular-highcharts';
import { VerbruikService } from '../verbruik.service';
import * as moment from 'moment';
import { IVerbruikPerMaand } from '../verbruikPerMaand.model';
import nl from '@angular/common/locales/nl';
import { registerLocaleData } from '@angular/common';
import { IVerbruikPerJaar } from '../verbruikPerJaar';

@Component({
  templateUrl: './verbruikPerJaar.component.html',
  styleUrls: ['./verbruikPerJaar.component.css']
})
export class VerbruikPerJaarComponent implements OnInit {
  chart = new Chart({
    chart: {
      type: 'column'
    },
    title: {
      text: 'Verbruik per jaar'
    },
    xAxis: {
      categories: []
    },
    yAxis: {
      min: 0,
      title: {
        text: 'Verbruik'
      },
      stackLabels: {
        enabled: true,
        style: {
          fontWeight: 'bold',
          color: 'gray'
        }
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
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: false,
          color: 'white'
        }
      }
    },
    series: []
  });

  verbruik: IVerbruikPerJaar[] = [];

  constructor(private verbruikService: VerbruikService) {
  }

  sortVerbruikDesc(a: IVerbruikPerJaar, b: IVerbruikPerJaar): number {
    if (a.jaar < b.jaar) return 1;
    if (a.jaar > b.jaar) return -1;

    return 0;
  }

  ngOnInit() {
    registerLocaleData(nl);

    this.chart.ref$.subscribe((chart) => {
      chart.showLoading();
    });

    this.verbruikService.getPerJaar().subscribe((verbruik) => {
      this.verbruik = verbruik.slice(0);

      this.verbruik.sort(this.sortVerbruikDesc);

      var categories = [];
      var gasSerie = { 'name': 'Gas (m3)', 'data': [], 'stack': 'gas', color: '#99ccff' };
      var waterSerie = { 'name': 'Water (m3)', 'data': [], 'stack': 'water', visible: false, color: 'blue' };
      var elektraE1Serie = { 'name': 'Elektra E1 (kWh)', 'data': [], 'stack': 'elektra' };
      var elektraE2Serie = { 'name': 'Elektra E2 (kWh)', 'data': [], 'stack': 'elektra' };

      verbruik.forEach(function (monthVerbruik) {
        categories.push(moment(new Date(monthVerbruik.jaar, 0)).format("YYYY"));
        gasSerie['data'].push(Math.round(monthVerbruik.gas));
        waterSerie['data'].push(Math.round(monthVerbruik.water));
        elektraE1Serie['data'].push(Math.round(monthVerbruik.elektraE1));
        elektraE2Serie['data'].push(Math.round(monthVerbruik.elektraE2));
      });

      this.chart.ref$.subscribe((chart) => {
        chart.hideLoading()
        chart.xAxis[0].setCategories(categories);
        chart.addSeries(gasSerie);
        chart.addSeries(waterSerie);
        chart.addSeries(elektraE1Serie);
        chart.addSeries(elektraE2Serie);
      });
    });
  }
  
  
}
