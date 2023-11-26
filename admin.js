import { initRuntime } from 'https://esm.sh/@master/css@beta';
import { createApp, ref, onBeforeMount, onMounted, watch, nextTick } from 'https://esm.sh/vue@3/dist/vue.esm-browser.prod.js';
import axios from 'https://esm.sh/axios?bundle';
import { basicSetup, EditorView } from 'https://esm.sh/codemirror';
import { EditorState } from 'https://esm.sh/@codemirror/state';
import { javascript } from 'https://esm.sh/@codemirror/lang-javascript';

const api = axios.create({
    baseURL: yosBrxMasterCSS.rest_api.url,
    headers: {
        'content-type': 'application/json',
        'accept': 'application/json',
        'X-WP-Nonce': yosBrxMasterCSS.rest_api.nonce,
    },
});

const app = createApp({
    setup() {
        const yosBrxMasterCSS = window.yosBrxMasterCSS;
        const editorEl = ref(null);
        let editorView;
        let updatingFromCode = false;
        const isBusy = ref(false);
        const showSuccess = ref(false);

        const versions = ref([]);
        const version = ref('');
        const presetGlobalStyles = ref(false);
        const masterCSSConfig = ref('');

        const saveConfig = async () => {
            isBusy.value = true;
            showSuccess.value = false;
            const { data } = await api.post(`${window.parent.yosBrxMasterCSS.rest_api.url}/store`, {
                version: version.value,
                presetGlobalStyles: presetGlobalStyles.value,
                masterCSSConfig: masterCSSConfig.value,
            });
            isBusy.value = false;
            showSuccess.value = true;
        };

        watch(masterCSSConfig, (newValue, oldValue) => {
            if (!updatingFromCode && newValue !== oldValue && editorView) {
                const currentState = editorView.state;
                const newState = currentState.update({
                    changes: { from: 0, to: currentState.doc.length, insert: newValue }
                });
                editorView.update([newState]);
            }
        });

        onBeforeMount(async () => {
            const { data: jsDeliverData } = await axios.get('https://data.jsdelivr.com/v1/package/npm/@master/css');
            versions.value = jsDeliverData.versions;

            const { data: loadedConfig } = await api.get(`${window.parent.yosBrxMasterCSS.rest_api.url}/index`);
            version.value = loadedConfig.version;
            presetGlobalStyles.value = loadedConfig.presetGlobalStyles;
            masterCSSConfig.value = loadedConfig.masterCSSConfig;
        });

        onMounted(() => {
            const updateListener = EditorView.updateListener.of((update) => {
                if (update.docChanged) {
                    updatingFromCode = true;
                    masterCSSConfig.value = update.state.doc.toString();
                    nextTick(() => {
                        updatingFromCode = false;
                    });
                }
            });

            const state = EditorState.create({
                doc: masterCSSConfig.value,
                extensions: [basicSetup, javascript(), updateListener]
            });

            editorView = new EditorView({
                state,
                parent: editorEl.value,
            });
        });

        return {
            yosBrxMasterCSS,
            versions,
            editorEl,
            version,
            presetGlobalStyles,
            masterCSSConfig,
            isBusy,
            showSuccess,
            saveConfig,
        };
    },
    /*html*/
    template: `
        <h1 class='wp-heading-inline'>Yabe Open Source - Bricks Master CSS</h1>
        <div class="p:8"><a href="https://rosua.org" target="_blank">Rosua.org</a> | <a href="https://yabe.land/" target="_blank">More plugins</a> | <a href="https://rosua.org/support-portal" target="_blank">Support</a> - <span tabindex="0">Version: {{ yosBrxMasterCSS._version }}</span></div>
        <hr class="wp-header-end" />
        <h2 class="nav-tab-wrapper">
            <a href="#/settings" class="nav-tab-active nav-tab" aria-current="page">Settings</a>
        </h2>
        <div class="yos-brx-master-css-content">
            <div class="mb:24">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Version</label></th>
                            <td>
                                <select v-model="version" class="min-w:150">
                                    <option v-for="v in versions" :value="v">{{ v }}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Preset Global Styles</label></th>
                            <td>
                                <input v-model="presetGlobalStyles" id="preset-global-styles" name="preset_global_styles" type="checkbox" /><label for="preset-global-styles" class="pl:4">load <code>@master/normal.css</code> stylesheet</label>
                                <p class="description">
                                    Normalize browser and preset global styles for more concise-style programming. <a href="https://beta.css.master.co/docs/global-styles" target="_blank">Learn more</a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Configuration</th>
                            <td>
                                <div id="master-css-editor" ref="editorEl" class="w:600 border:1|solid|silver"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex align-items:center">
                <button type="button" @click="saveConfig" class="button button-primary p-ripple">Save Changes<span class="p-ink" role="presentation" aria-hidden="true"></span></button>
                <span :class="isBusy ? 'visible' : 'hidden'" class="spinner"></span>
                <span v-if="showSuccess" class="ml:8 font:semibold">Saved successfully.</span>
            </div>
        </div>
    `
});

document.addEventListener('DOMContentLoaded', () => {
    initRuntime({});
    app.mount('#yos-brx-master-css-app');
});












