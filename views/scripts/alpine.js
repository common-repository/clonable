document.addEventListener('alpine:init', () => {
    Alpine.data('languageTags', (languageTags) => ({
        languageTagData: languageTags,
        init() {
            if (languageTags == null) {
                // if the language tags are empty, use some decent default
                this.languageTagData = {
                    version: 2,
                    data: {
                        original: {
                            domain: window.location.hostname,
                            langcode: 'nl-nl',
                            original_subfolder: "/",
                            clone_subfolder: "/",
                            include: true
                        },
                        clones: []
                    }
                }
            }
        },
        addRow() {
            let data = {
                domain: "",
                langcode: "",
                original_subfolder: "/",
                clone_subfolder: "/",
            }
            this.languageTagData.data.clones.push(data);
            jQuery(document).ready(function ($) {
                $('.ui.dropdown')
                    .dropdown({ fullTextSearch: true })
                ;
            })
        },
        removeRow(index) {
            this.languageTagData.data.clones.splice(index, 1);
        }
    }));

    Alpine.data('languageSwitcher', (languages) => ({
        languages: languages ?? [],
        addRow() {
            let data = {
                clonableLocaleCode: "de_DE",
                clonableDisplayLanguage: "German (Germany)",
                clonableUrl: "https://",
            }
            this.languages.push(data);
            jQuery(document).ready(function ($) {
                $('.ui.dropdown')
                    .dropdown({ fullTextSearch: true })
                ;
            })
        },
        removeRow(index) {
            this.languages.splice(index, 1);
        },
        selectFlag(index, locale, display) {
            this.languages[index].clonableLocaleCode = locale;
            this.languages[index].clonableDisplayLanguage = display;
        }
    }));

    Alpine.data('wooCommerceAllowedOrigins', (allowedOrigins) => ({
        origins: allowedOrigins ?? [],
        addRow() {
            this.origins.push("")
        },
        removeRow(index) {
            this.origins.splice(index, 1);
        },
    }))
})