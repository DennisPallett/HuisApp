import { Component, OnInit } from '@angular/core';
import { AfschriftenService } from '../afschriften.service';

@Component({
  templateUrl: './importeren.component.html'
})
export class ImporterenComponent implements OnInit {
  selectedFiles: FileList = null;

  constructor(
    private afschriftenService: AfschriftenService) {
  }

  ngOnInit() {
    
  }

  handleFileInput(files: FileList) {
    console.log(files);
    this.selectedFiles = files;
  }

  startImport() {

  }

}
