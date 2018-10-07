import { NgModule } from '@angular/core'; 
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { TemperatuurService } from './temperatuur.service';
import { MonthNamePipe } from '../shared/monthname';
import { ImporterenComponent } from './importeren/importeren.component';
import { ChartModule } from 'angular-highcharts';
import { MaandOverzichtComponent } from './maandoverzicht/maandoverzicht.component';
import { DagOverzichtComponent } from './dagoverzicht/dagoverzicht.component';


@NgModule({
  declarations: [
    ImporterenComponent,
    MaandOverzichtComponent,
    DagOverzichtComponent
  ],
  imports: [
    SharedModule,
    ChartModule,
    RouterModule.forChild([
      { path: 'temperatuur/importeren', component: ImporterenComponent },
      { path: 'temperatuur/maandoverzicht', component: MaandOverzichtComponent },
      { path: 'temperatuur/dagoverzicht', component: DagOverzichtComponent }
    ])
  ],
  providers: [TemperatuurService, MonthNamePipe]
})
export class TemperatuurModule { }
