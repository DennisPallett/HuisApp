import { NgModule } from '@angular/core'; 
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { TemperatuurService } from './temperatuur.service';
import { MonthNamePipe } from '../shared/monthname';
import { ImporterenComponent } from './importeren/importeren.component';
import { ChartModule } from 'angular-highcharts';


@NgModule({
  declarations: [
    ImporterenComponent
  ],
  imports: [
    SharedModule,
    ChartModule,
    RouterModule.forChild([
      { path: 'temperatuur/importeren', component: ImporterenComponent }
    ])
  ],
  providers: [TemperatuurService, MonthNamePipe]
})
export class TemperatuurModule { }
