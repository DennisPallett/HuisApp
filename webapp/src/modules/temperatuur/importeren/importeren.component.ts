import { Component, OnInit } from '@angular/core';
import { TemperatuurService } from '../temperatuur.service';
import { IImportResult } from '../importresult.model';

@Component({
  templateUrl: './importeren.component.html'
})
export class ImporterenComponent implements OnInit {
  selectedFiles: FileList = null;

  importInProgress: boolean = false;

  importResult: IImportResult = null;

  importError: string = null;

  importErrorCode: number = null;

  constructor(
    private temperatuurService: TemperatuurService) {
  }

  ngOnInit() {
    this.importInProgress = false;
    this.importResult = null;
  }

  handleFileInput(files: FileList) {
    this.selectedFiles = files;
  }

  startImport() {
    if (this.selectedFiles.length == 0) return false;

    this.importInProgress = true;
    this.importError = null;
    this.importErrorCode = null;

    this.temperatuurService.importFiles(this.selectedFiles).subscribe((result) => {
      this.importInProgress = false;
      this.importResult = result;
    }, (response) => {
      this.importInProgress = false;
      this.importError = response.error.message;
      this.importErrorCode = response.error.code;
    });
  }

}
