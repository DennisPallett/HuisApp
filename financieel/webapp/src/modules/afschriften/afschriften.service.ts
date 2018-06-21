import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { map } from 'rxjs/operators';
import { environment } from "../../environments/environment";
import { IAfschrift } from "./afschrift.model";

@Injectable()
export class AfschriftenService {

  private apiAction: string = "statements";

  constructor(private httpClient: HttpClient) {
  }

  getAllAfschriften(): Observable<IAfschrift[]> {
    return this.httpClient.get<IAfschrift[]>(environment.apiUrl + this.apiAction);
  }

  getAfschriftenForMonth(month: number, year: number): Observable<IAfschrift[]> {
    return this.getAfschriftenForMonthSortedBy(month, year, "start_balance_date", "ASC");
  }

  getAfschriftenForMonthSortedBy(month: number, year: number, sortBy: string, sortOrder: string): Observable<IAfschrift[]> {
    return this.httpClient.get<IAfschrift[]>(environment.apiUrl + this.apiAction, {
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
