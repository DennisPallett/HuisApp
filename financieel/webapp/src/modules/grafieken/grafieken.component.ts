import { Component, OnInit } from '@angular/core';
import { TransactiesService } from '../shared/transacties.service';
import { ITransactie } from '../shared/transactie.model';
import { forEach } from '@angular/router/src/utils/collection';
import { DatesService } from '../shared/dates.service';
import { IMonth } from '../shared/month.model';
import { Chart } from 'angular-highcharts';
import * as Highcharts from 'highcharts';

@Component({
  templateUrl: './grafieken.component.html',
  styleUrls: ['./grafieken.component.css']
})
export class GrafiekenComponent implements OnInit {
  chart = new Chart({
    chart: {
      type: 'column'
    },
    title: {
      text: 'Lasten/inkomen per maand'
    },
    xAxis: {
      categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
    },
    yAxis: {
      min: 0,
      title: {
        text: 'Bedrag'
      },
      stackLabels: {
        enabled: true,
        style: {
          fontWeight: 'bold',
          color: 'gray'
        }
      }//,
      //labels: {
      //  formatter: function () {
       //   return '€ ' + this.axis.defaultLabelFormatter.call(this);
        //}
      //}
    },
    legend: {
      align: 'right',
      x: -30,
      verticalAlign: 'top',
      y: 25,
      floating: true,
      backgroundColor: 'white',
      borderColor: '#CCC',
      borderWidth: 1,
      shadow: false
    },
    tooltip: {
      headerFormat: '<b>{point.x}</b><br/>',
      pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: true,
          color: 'white'
        }
      }
    },
    series: [{
      name: 'John',
      data: [5, 3, 4, 7, 2]
    }, {
      name: 'Jane',
      data: [2, 2, 3, 2, 1]
    }, {
      name: 'Joe',
      data: [3, 4, 4, 2, 5]
    }]
  });

  constructor(private transactiesService: TransactiesService, private datesService: DatesService) {
  }

  ngOnInit() {
    this.chart.ref$.subscribe((chart) => {
      chart.showLoading();
    });
  }
  

}
