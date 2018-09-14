import { Component, OnInit } from '@angular/core';
import { MeterstandenService } from '../meterstanden.service';
import { IMeterstand } from '../meterstand.model';

@Component({
  templateUrl: './invoeren.component.html'
})
export class InvoerenComponent implements OnInit {

  errorMessage: string = null;

  errorCode: number = null;

  newMeterstand: IMeterstand = {} as IMeterstand;

  submitted: boolean = false;

  result: boolean = false;

  constructor(
    private meterstandenService: MeterstandenService) {
  }

  ngOnInit() {
  }

  onSubmit() {
    this.submitted = true;

    this.errorMessage = null;
    this.errorCode = null;

    this.meterstandenService.insertMeterstand(this.newMeterstand).subscribe((result) => {
      this.result = result;
    }, (response) => {
      this.result = false;
      this.errorMessage = response.error.message;
      this.errorCode = response.error.code;
    });
  }

}
