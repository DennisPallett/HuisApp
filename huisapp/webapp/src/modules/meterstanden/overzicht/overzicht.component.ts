import { Component, OnInit } from '@angular/core';
import { MeterstandenService } from '../meterstanden.service';
import { IMeterstand } from '../meterstand.model';
import * as moment from 'moment';
import nl from '@angular/common/locales/nl';
import { registerLocaleData } from '@angular/common';

@Component({
  templateUrl: './overzicht.component.html',
  styleUrls: ['./overzicht.component.css']
})
export class OverzichtComponent implements OnInit {

  meterstanden: IMeterstand[] = [];

  constructor(
    private meterstandenService: MeterstandenService) {
  }

  ngOnInit() {
    registerLocaleData(nl);
    this.loadMeterstanden();
  }

  private loadMeterstanden() {
    this.meterstandenService.getMeterstanden().subscribe((meterstanden) => {
      this.meterstanden = meterstanden;
    });
  }

  public deleteMeterstand(meterstand: IMeterstand) {
    if (!window.confirm("Weet je zeker dat je de meterstand voor " + moment(meterstand.opnameDatum).format('D MMMM YYYY') + " wilt verwijderen?"))
      return;

    this.meterstandenService.delete(meterstand).subscribe((result) => {
      this.loadMeterstanden();
    })

  }
  
}
