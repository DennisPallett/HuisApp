import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { DatesService } from './dates.service';
import { MonthNamePipe } from './monthname';
import { MomentModule } from 'ngx-moment';
import 'moment/locale/nl';

@NgModule({
  declarations: [
    MonthNamePipe
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpClientModule,
    BrowserModule,
    MomentModule
  ],
  exports: [
    FormsModule,
    HttpClientModule,
    BrowserModule,
    MonthNamePipe,
    MomentModule
  ],
  providers: [DatesService]
})
export class SharedModule { }
