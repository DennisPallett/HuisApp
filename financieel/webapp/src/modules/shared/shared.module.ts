import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { MailComponent } from './mail.component';
import { ConvertToSpacesPipe } from './converttospaces.pipe';

@NgModule({
  declarations: [
    ConvertToSpacesPipe,
    MailComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpClientModule,
    BrowserModule
  ],
  exports: [
    ConvertToSpacesPipe,
    MailComponent,
    FormsModule,
    HttpClientModule,
    BrowserModule
  ],
  providers: []
})
export class SharedModule { }
