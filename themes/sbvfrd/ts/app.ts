import * as $ from "jquery";
import {Hydra} from "./Hydra";
import {AutoSuggest, AutoSuggestConfiguration} from "./AutoSuggest";

$(document).ready(() => {
    const client = new Hydra("http://data.swissbib.ch/");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;
    const list: HTMLElement = $(".sidebar .list-group")[0];
    const template: any = (p: any) => {
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=${p.lastName},
${p.firstName}&amp;type=Author" title=" ${p.lastName}, ${p.firstName}">${p.lastName}, ${p.firstName}</a>
<span ${ Hydra.personHasSufficientData(p) ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"
authorid="${p["@id"]}"></span></li>`;
    };

    if (recordIdEl) {
        client.renderContributors(recordIdEl.value, list, template);
    }

    // initialize auto-suggest
    const autoSuggestConfiguration: AutoSuggestConfiguration = new AutoSuggestConfiguration ([
        { label: "Abschnitt 01", limit: 3},
        { label: "Abschnitt 02", limit: 4},
        { label: "Abschnitt 03", limit: 5}
    ]);

    const autoSuggest = new AutoSuggest("#searchForm_lookfor", autoSuggestConfiguration);
    autoSuggest.initialize();
});
