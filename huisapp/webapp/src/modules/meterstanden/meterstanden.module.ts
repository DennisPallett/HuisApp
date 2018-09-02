import { NgModule } from '@angular/core'; 
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { OverzichtComponent } from './overzicht/overzicht.component';
import { MeterstandenService } from './meterstanden.service';
import { MonthNamePipe } from '../shared/monthname';
import { InvoerenComponent } from './invoeren/invoeren.component';
import { AanpassenComponent } from './aanpassen/aanpassen.component';


@NgModule({
  declarations: [
    OverzichtComponent,
    InvoerenComponent,
    AanpassenComponent
  ],
  imports: [
    SharedModule,
    RouterModule.forChild([
      { path: 'meterstanden', component: OverzichtComponent },
      { path: 'meterstanden/invoeren', component: InvoerenComponent },
      { path: 'meterstanden/aanpassen/:opnameDatum', component: AanpassenComponent }
    ])
  ],
  providers: [MeterstandenService, MonthNamePipe]
})
export class MeterstandenModule { }
