import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { map } from 'rxjs/operators';
import { environment } from "../../environments/environment";
import { IMeterstand } from "./meterstand.model";

@Injectable()
export class MeterstandenService {

  private apiAction: string = "meterstanden";

  constructor(private httpClient: HttpClient) {
  }

  delete(month: number, year: number): Observable<boolean> {
    return this.httpClient.post<boolean>(environment.apiUrl + this.apiAction + "/delete", {
      month: month,
      year: year
    });
  }

  getAllAfschriften(): Observable<IMeterstand[]> {
    return this.httpClient.get<IMeterstand[]>(environment.apiUrl + this.apiAction);
  }

  getAfschriftenForMonth(month: number, year: number): Observable<IMeterstand[]> {
    return this.getAfschriftenForMonthSortedBy(month, year, "start_balance_date", "ASC");
  }

  getAfschriftenForMonthSortedBy(month: number, year: number, sortBy: string, sortOrder: string): Observable<IMeterstand[]> {
    return this.httpClient.get<IMeterstand[]>(environment.apiUrl + this.apiAction, {
      params:
        {
          month: month.toString(),
          year: year.toString(),
          sortby: sortBy,
          sortorder: sortOrder
        }
    });
  }

  updateCategory(transactieId: number, category: string): Observable<boolean> {
    return this.httpClient.post<boolean>(environment.apiUrl + this.apiAction + "/update-category", {
      id: transactieId,
      category: category 
    });
  }

}
