import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { catchError, retry, tap } from 'rxjs/operators';

@Injectable({
    providedIn: 'root'
})
export class StatsApiService
{
    private headers: HttpHeaders = new HttpHeaders({ 'Content-Type': 'application/json' });
    private baseUrl = 'https://api.ortsarchiv-gemeinlebarn.org/v3';

    constructor(private http: HttpClient) { }

    public read(): Observable<any>
    {
        return this.http
            .get(`${this.baseUrl}/stats/public`, { headers: this.headers })
            .pipe(
                retry(1)
            );
    }
}
