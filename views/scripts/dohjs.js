window.addEventListener('DOMContentLoaded', () => {
    const resolver = new doh.DohResolver('https://1.1.1.1/dns-query');

    const domain = document.querySelector("#clonable_site_domain").value;
    const preferredDomain = document.querySelector("#clonable_site_preferred_domain").value;
    const siteOriginInput = document.querySelector("#clonable_site_origin");

    let domainToLookup;
    if (preferredDomain === 'www') {
        // We prefer www
        domainToLookup = 'www.' + domain;
    } else {
        // We prefer non-www, or we could not find the preferred domain
        domainToLookup = domain;
    }

    // First try ipv6, then ipv4
    resolver.query(domainToLookup, 'AAAA')
        .then(response => {
            if (response.answers.length > 0) {
                for (let i = 0; i < response.answers.length; i++) {
                    // Do this check to make sure we don't use a CNAME entry
                    if (response.answers[i].type === 'AAAA') {
                        // We found an AAAA record
                        siteOriginInput.value = response.answers[i].data;
                        break;
                    }
                }
            } else {
                // No AAAA records found, try A
                resolver.query(domainToLookup, 'A')
                    .then(response => {
                        for (let i = 0; i < response.answers.length; i++) {
                            // Do this check to make sure we don't use a CNAME entry
                            if (response.answers[i].type === 'A') {
                                // We found an A record
                                siteOriginInput.value = response.answers[i].data;
                                break;
                            }
                        }
                    })
                    .catch(err => console.error(err));
            }
        })
        .catch(err => console.error(err));
})