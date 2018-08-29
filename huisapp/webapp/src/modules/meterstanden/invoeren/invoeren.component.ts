import { Component, OnInit } from '@angular/core';
import { MeterstandenService } from '../meterstanden.service';
import { IMeterstand } from '../meterstand.model';

@Component({
  templateUrl: './invoeren.component.html'
})
export class InvoerenComponent implements OnInit {
  selectedFiles: FileList = null;

  importInProgress: boolean = false;

  importError: string = null;

  importErrorCode: number = null;

  newMeterstand: IMeterstand = {} as IMeterstand;

  submitted: boolean = false;

  constructor(
    private meterstandenService: MeterstandenService) {
  }

  ngOnInit() {
    this.importInProgress = false;
  }

  onSubmit() {
    this.submitted = true;
    console.log("YES: " + this.newMeterstand.opnameDatum);
  }

  handleFileInput(files: FileList) {
    this.selectedFiles = files;
  }

  startImport() {
    if (this.selectedFiles.length == 0) return false;

    this.importInProgress = true;
    this.importError = null;
    this.importErrorCode = null;

    this.meterstandenService.updateCategory(1, "test").subscribe((result) => {
      this.importInProgress = false;
    }, (error) => {
      this.importInProgress = false;
      this.importError = error.error;
      this.importErrorCode = error.status;
    });
  }

}
