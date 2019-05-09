import { NgModule } from '@angular/core'; 
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { OverzichtComponent } from './overzicht/overzicht.component';
import { MeterstandenService } from './meterstanden.service';
import { MonthNamePipe } from '../shared/monthname';
import { InvoerenComponent } from './invoeren/invoeren.component';
import { AanpassenComponent } from './aanpassen/aanpassen.component';
import { ChartModule } from 'angular-highcharts';
import { VerbruikService } from './verbruik.service';
import { VerbruikComponent } from './verbruik/verbruik.component';
import { VerbruikPerJaarComponent } from './verbruikPerJaar/verbruikPerJaar.component';


@NgModule({
  declarations: [
    OverzichtComponent,
    InvoerenComponent,
    AanpassenComponent,
    VerbruikComponent,
    VerbruikPerJaarComponent
  ],
  imports: [
    SharedModule,
    ChartModule,
    RouterModule.forChild([
      { path: 'meterstanden', component: OverzichtComponent },
      { path: 'meterstanden/invoeren', component: InvoerenComponent },
      { path: 'meterstanden/aanpassen/:opnameDatum', component: AanpassenComponent },
      { path: 'meterstanden/verbruik/maand', component: VerbruikComponent },
      { path: 'meterstanden/verbruik/jaar', component: VerbruikPerJaarComponent },
    ])
  ],
  providers: [MeterstandenService, MonthNamePipe, VerbruikService]
})
export class MeterstandenModule { }
