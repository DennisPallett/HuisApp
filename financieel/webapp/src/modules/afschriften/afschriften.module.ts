import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { SharedModule } from '../shared/shared.module';

import { AfschriftenComponent } from './afschriften.component';
import { AfschriftenService } from './afschriften.service';


@NgModule({
  declarations: [
    AfschriftenComponent
  ],
  imports: [
    SharedModule,
    RouterModule.forChild([
      { path: 'afschriften', component: AfschriftenComponent }
    ])
  ],
  providers: [AfschriftenService]
})
export class AfschriftenModule { }
