import { Component, OnInit } from '@angular/core';
import { TransactiesService } from '../shared/transacties.service';
import { ITransactie } from '../shared/transactie.model';
import { forEach } from '@angular/router/src/utils/collection';
import { DatesService } from '../shared/dates.service';
import { IMonth } from '../shared/month.model';
import { CategoriesService } from '../shared/categories.service';
import { ICategory } from '../shared/category.model';

@Component({
  templateUrl: './transacties.component.html',
  styleUrls: ['./transacties.component.css']
})
export class TransactiesComponent implements OnInit {
  availableMonths: IMonth[] = [];

  categories: ICategory[] = [];

  transacties: ITransactie[] = [];

  currentMonthYear: string = '';

  currentMonth: number = 0;

  currentYear: number = 0;

  constructor(private transactiesService: TransactiesService, private datesService: DatesService, private categoriesService: CategoriesService) {
  }

  ngOnInit() {
    this.datesService.getMonths().subscribe((months) => {
      this.availableMonths = months;
    });

    this.categoriesService.getCategories().subscribe((categories) => {
      this.categories = categories;
    })
  }

  public changeMonth() {
    var split = this.currentMonthYear.split('-');
    this.currentMonth = parseInt(split[0]);
    this.currentYear = parseInt(split[1]);

    this.loadTransacties();
  }

  private loadTransacties() {
    this.transactiesService.getTransactiesForMonthSortedBy(this.currentMonth, this.currentYear, "value_date", "desc").subscribe(
      (transacties) => {
        this.transacties = transacties;
      },
      (error) => {
        console.log(error);
      });
  }
  
}
