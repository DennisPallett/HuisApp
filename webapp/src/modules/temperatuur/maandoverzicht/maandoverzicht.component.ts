import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import nl from '@angular/common/locales/nl';
import { registerLocaleData } from '@angular/common';
import { TemperatuurService } from '../temperatuur.service';
import { ITemperatuurPerMaand } from '../temperatuurPerMaand.model';

@Component({
  templateUrl: './maandoverzicht.component.html',
  styleUrls: ['./maandoverzicht.component.css']
})
export class MaandOverzichtComponent implements OnInit {

  temperaturen: ITemperatuurPerMaand[] = [];

  constructor(
    private temperatuurService: TemperatuurService) {
  }

  ngOnInit() {
    registerLocaleData(nl);
    this.loadTemperaturen();
  }

  private loadTemperaturen() {
    this.temperatuurService.getTemperatuurPerMaand().subscribe((temperaturen) => {
      this.temperaturen = temperaturen;
    });
  }
  
}
