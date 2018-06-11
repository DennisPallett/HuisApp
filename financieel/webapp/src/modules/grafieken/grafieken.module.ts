import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';
import { ChartModule } from 'angular-highcharts';

import { GrafiekenComponent } from './grafieken.component';


@NgModule({
  declarations: [
    GrafiekenComponent
  ],
  imports: [
    SharedModule,
    ChartModule,
    RouterModule.forChild([
      { path: 'grafieken', component: GrafiekenComponent }
    ])
  ],
  providers: []
})
export class GrafiekenModule { }
