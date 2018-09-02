import { Component, OnInit } from '@angular/core';
import { MeterstandenService } from '../meterstanden.service';
import { IMeterstand } from '../meterstand.model';
import { ActivatedRoute } from '@angular/router';

@Component({
  templateUrl: './aanpassen.component.html'
})
export class AanpassenComponent implements OnInit {
  opnameDatum: string = null;

  errorMessage: string = null;

  errorCode: number = null;

  meterstand: IMeterstand = {} as IMeterstand;

  submitted: boolean = false;

  result: boolean = false;

  constructor(
    private meterstandenService: MeterstandenService,
    private route: ActivatedRoute) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.opnameDatum = params['opnameDatum'];

      this.meterstandenService.getMeterstand(this.opnameDatum).subscribe(meterstand => {
        if (typeof (meterstand) == 'undefined') {
          alert('Unable to find meterstand!')
          return;
        }
        this.meterstand = meterstand;
      })
    });
  }

  onSubmit() {
    this.submitted = true;

    this.errorMessage = null;
    this.errorCode = null;

    this.meterstandenService.saveMeterstand(this.meterstand).subscribe((result) => {
      this.result = result;
    }, (response) => {
      this.result = false;
      this.errorMessage = response.error.message;
      this.errorCode = response.error.code;
    });
  }

}
