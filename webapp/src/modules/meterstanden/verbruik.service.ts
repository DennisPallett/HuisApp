import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { environment } from "../../environments/environment";
import { IVerbruikPerMaand } from "./verbruikPerMaand.model";
import { IVerbruikPerJaar } from "./verbruikPerJaar";

@Injectable()
export class VerbruikService {

  private apiAction: string = "verbruik";

  constructor(private httpClient: HttpClient) {
  }

  getPerMaand(): Observable<IVerbruikPerMaand[]> {
    return this.httpClient.get<IVerbruikPerMaand[]>(environment.apiUrl + this.apiAction + "/per-maand");
  }

  getPerJaar(): Observable<IVerbruikPerJaar[]> {
    return this.httpClient.get<IVerbruikPerJaar[]>(environment.apiUrl + this.apiAction + "/per-jaar");
  }
  
}
