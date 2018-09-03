import { NgModule } from '@angular/core'; 
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { OverzichtComponent } from './overzicht/overzicht.component';
import { MeterstandenService } from './meterstanden.service';
import { MonthNamePipe } from '../shared/monthname';
import { InvoerenComponent } from './invoeren/invoeren.component';
import { AanpassenComponent } from './aanpassen/aanpassen.component';
import { GrafiekenComponent } from './grafieken/grafieken.component';
import { GasVerbruikChartComponent } from './grafieken/gasverbruikChart.component';
import { ChartModule } from 'angular-highcharts';
import { VerbruikService } from './verbruik.service';


@NgModule({
  declarations: [
    OverzichtComponent,
    InvoerenComponent,
    AanpassenComponent,
    GrafiekenComponent,
    GasVerbruikChartComponent
  ],
  imports: [
    SharedModule,
    ChartModule,
    RouterModule.forChild([
      { path: 'meterstanden', component: OverzichtComponent },
      { path: 'meterstanden/invoeren', component: InvoerenComponent },
      { path: 'meterstanden/aanpassen/:opnameDatum', component: AanpassenComponent },
      { path: 'meterstanden/grafieken', component: GrafiekenComponent },
    ])
  ],
  providers: [MeterstandenService, MonthNamePipe, VerbruikService]
})
export class MeterstandenModule { }
