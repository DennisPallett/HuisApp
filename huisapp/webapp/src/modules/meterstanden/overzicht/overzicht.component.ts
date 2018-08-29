import { Component, OnInit } from '@angular/core';
import { forEach } from '@angular/router/src/utils/collection';
import { DatesService } from '../../shared/dates.service';
import { IMonth } from '../../shared/month.model';
import { MeterstandenService } from '../meterstanden.service';
import { IMeterstand } from '../meterstand.model';
import { MonthNamePipe } from '../../shared/monthname';

@Component({
  templateUrl: './overzicht.component.html',
  styleUrls: ['./overzicht.component.css']
})
export class OverzichtComponent implements OnInit {
  availableMonths: IMonth[] = [];

  meterstanden: IMeterstand[] = [];

  currentMonthYear: string = '';

  currentMonth: number = 0;

  currentYear: number = 0;

  onlyShowUncategorized: boolean = false;

  constructor(
    private meterstandenService: MeterstandenService,
    private datesService: DatesService,
    private monthName: MonthNamePipe) {
  }

  ngOnInit() {
    this.datesService.getMonths().subscribe((months) => {
      this.availableMonths = months;
    });
  }

  public changeMonth() {
    var split = this.currentMonthYear.split('-');
    this.currentMonth = parseInt(split[0]);
    this.currentYear = parseInt(split[1]);

    this.loadAfschriften();
  }

  public deleteAfschriften() {
    if (!window.confirm("Weet je zeker dat je alle afschriften voor "
      + this.monthName.transform(this.currentMonth) + " " + this.currentYear + " wilt verwijderen?"))
      return;

    //this.afschriftenService.delete(this.currentMonth, this.currentYear).subscribe((result) => {
    //  this.loadAfschriften();
    //})
  }

  private loadAfschriften() {
    this.meterstandenService.getAfschriftenForMonth(this.currentMonth, this.currentYear).subscribe(
      (afschriften) => {
        this.meterstanden = afschriften;
      },
      (error) => {
        console.log(error);
      });
  }
  
}
