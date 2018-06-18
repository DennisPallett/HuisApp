import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { TransactiesComponent } from './transacties.component';


@NgModule({
  declarations: [
    TransactiesComponent
  ],
  imports: [
    SharedModule,
    RouterModule.forChild([
      { path: 'transacties', component: TransactiesComponent }
    ])
  ],
  providers: []
})
export class TransactiesModule { }
