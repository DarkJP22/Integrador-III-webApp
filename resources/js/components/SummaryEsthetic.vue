<template>
  <div class="sidebar-bg" :class="{ open: showSummary }" @click.self="showSummary = false">

    <div class="summary">
      <button type="button" class="btn btn-primary btn-open-summary" @click="showSummary = !showSummary">
        {{ buttonName }}
      </button>
      <div class="buttons-summary pull-right">
        <a :href="'/beautician/agenda/appointments/' + appointment.id + '/print'" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i></a>
        <a :href="'/beautician/agenda/appointments/' + appointment.id + '/pdf'" class="btn btn-secondary btn-sm" target="_blank">PDF</a>
      </div>
      <div class="box2 box-solid">
        <div class="box-header with-border">
          <i class="fa fa-book"></i>

          <h3 class="box-title">
            <slot>Resumen</slot>
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body summary-flex">
          <dl class="summary-dl">
            <dt class="text-aqua">
              <h4>Evaluacion Física</h4>
            </dt>
            <dd>
              <div v-if="summary.evaluation.facial.length">
                <b>Facial:</b>
                <div v-for="(item, index) in summary.evaluation.facial" :key="index">
                  <span>{{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.evaluation.facialNotes">
                {{ summary.evaluation.facialNotes.notes }}
              </div>
              <div v-if="summary.evaluation.corporal.length">
                <b>Corporal:</b>
                <div v-for="(item, index) in summary.evaluation.corporal" :key="index">
                  <span v-if="item.zone">{{ item.name }} en {{ item.zone }}</span>
                </div>
              </div>
              <div v-if="summary.evaluation.corporalNotes">
                {{ summary.evaluation.corporalNotes.notes }}
              </div>
              <div v-if="summary.evaluation.depilacion.length">
                <b>Depilación:</b>
                <div v-for="(item, index) in summary.evaluation.depilacion" :key="index">
                  <span>{{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.evaluation.depilacionNotes">
                {{ summary.evaluation.depilacionNotes.notes }}
              </div>
            </dd>

            <dt class="text-aqua">
              <h4>Antropometria</h4>
            </dt>
            <dd>
              <div v-if="
                summary.anthropometry.items.height &&
                summary.anthropometry.items.height.length
              ">
                <b>Altura:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items.height" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.weight &&
                summary.anthropometry.items.weight.length
              ">
                <b>Peso:</b> <br />
                <span v-for="(item, index) in summary.anthropometry.items.weight" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.mass &&
                summary.anthropometry.items.mass.length
              ">
                <b>IMC:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items.mass" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.pa &&
                summary.anthropometry.items.pa.length
              ">
                <b>P.A:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items.pa" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.abdomen_alto &&
                summary.anthropometry.items.abdomen_alto.length
              ">
                <b>Abdomen Alto:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .abdomen_alto" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.ombligo &&
                summary.anthropometry.items.ombligo.length
              ">
                <b>Ombligo:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items.ombligo" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.abdomen_bajo &&
                summary.anthropometry.items.abdomen_bajo.length
              ">
                <b>Abdomen Bajo:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .abdomen_bajo" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.abdomen_bajo_bajo &&
                summary.anthropometry.items.abdomen_bajo_bajo.length
              ">
                <b>Abdomen Bajo Bajo:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .abdomen_bajo_bajo" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.piernas_alta &&
                summary.anthropometry.items.piernas_alta.length
              ">
                <b>Piernas Altas:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .piernas_alta" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.piernas_baja &&
                summary.anthropometry.items.piernas_baja.length
              ">
                <b>Piernas Bajas:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .piernas_baja" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.brazos_alto &&
                summary.anthropometry.items.brazos_alto.length
              ">
                <b>Brazos Alto:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .brazos_alto" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.brazos_medio &&
                summary.anthropometry.items.brazos_medio.length
              ">
                <b>Brazos Medio:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .brazos_medio" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
              <div v-if="
                summary.anthropometry.items.brazos_bajo &&
                summary.anthropometry.items.brazos_bajo.length
              ">
                <b>Brazos Bajo:</b><br />
                <span v-for="(item, index) in summary.anthropometry.items
                .brazos_bajo" :key="index"><span v-if="item.value" class="margin">S{{ index + 1 }}: {{ item.value }}</span></span>
              </div>
            </dd>
            <dt class="text-aqua">
              <h4>Tratamiento</h4>
            </dt>
            <dd>
              <!-- <div v-if="summary.treatments.length">
                <div v-for="(item, index) in summary.treatments" :key="index"><span>{{ item.sesions }} x {{ item.name }}</span></div>
              </div> -->
              <div v-if="summary.estreatment.facial.length">
                <b>Facial:</b>
                <div v-for="(item, index) in summary.estreatment.facial" :key="index">
                  <span>{{ item.sessions }} x {{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.estreatment.facialNotes">
                {{ summary.estreatment.facialNotes.notes }}
              </div>
              <div v-if="summary.estreatment.corporal.length">
                <b>Corporal:</b>
                <div v-for="(item, index) in summary.estreatment.corporal" :key="index">
                  <span>{{ item.sessions }} x {{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.estreatment.corporalNotes">
                {{ summary.estreatment.corporalNotes.notes }}
              </div>
              <div v-if="summary.estreatment.depilacion.length">
                <b>Depilación:</b>
                <div v-for="(item, index) in summary.estreatment.depilacion" :key="index">
                  <span>{{ item.sessions }} x {{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.estreatment.depilacionNotes">
                {{ summary.estreatment.depilacionNotes.notes }}
              </div>
            </dd>

            <dt class="text-aqua">
              <h4>Recomendaciones</h4>
            </dt>
            <dd>
              <div v-if="summary.recomendation.facial.length">
                <b>Facial:</b>
                <div v-for="(item, index) in summary.recomendation.facial" :key="index">
                  <span>{{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.recomendation.facialNotes">
                {{ summary.recomendation.facialNotes.notes }}
              </div>
              <div v-if="summary.recomendation.corporal.length">
                <b>Corporal:</b>
                <div v-for="(item, index) in summary.recomendation.corporal" :key="index">
                  <span>{{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.recomendation.corporalNotes">
                {{ summary.recomendation.corporalNotes.notes }}
              </div>
              <div v-if="summary.recomendation.depilacion.length">
                <b>Depilación:</b>
                <div v-for="(item, index) in summary.recomendation.depilacion" :key="index">
                  <span>{{ item.name }}</span>
                </div>
              </div>
              <div v-if="summary.recomendation.depilacionNotes">
                {{ summary.recomendation.depilacionNotes.notes }}
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.sidebar-bg {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  width: 0;
  height: 0;
  background-color: rgba(0, 0, 0, 0.2);
  z-index: 1200;
}

.sidebar-bg.open {
  width: 100%;
  height: 100%;
}

.summary {
  position: fixed;
  top: 0;
  bottom: 0;
  right: -300px;
  background-color: white;
  box-shadow: 1px 2px 10px rgba(0, 0, 0, 0.2);
  z-index: 99;
  width: 300px;
  height: 100vh;
  transition: all 0.3s ease;
}

.summary .box2 {
  width: 100%;
  height: 100%;
  overflow-y: auto;
}

.sidebar-bg.open .summary {
  right: 0px;
}

.btn-open-summary {
  position: relative;
  top: 50%;
  transform: translateY(-50%) rotate(90deg);
  right: 57px;
  z-index: 99;
}
</style>
<script>
export default {
    props: [
        'appointment',
        'patient',
        'evaluations',
        'evaluationNotes',
        'anthropometry',
        'estreatments',
        'treatmentNotes',
        'recomendations',
        'recomendationNotes',
    ],
    data() {
        return {
            showSummary: false,
            summary: {
                evaluation: {
                    facial: [],
                    facialNotes: {},
                    corporal: [],
                    corporalNotes: {},
                    depilacion: [],
                    depilacionNotes: {},
                },
                anthropometry: {
                    items: [],
                },
                recomendation: {
                    facial: [],
                    facialNotes: {},
                    corporal: [],
                    corporalNotes: {},
                    depilacion: [],
                    depilacionNotes: {},
                },
                // treatments:[],
                estreatment: {
                    facial: [],
                    facialNotes: {},
                    corporal: [],
                    corporalNotes: {},
                    depilacion: [],
                    depilacionNotes: {},
                },
            },
            loader: false,
        };
    },
    computed: {
        buttonName() {
            return this.showSummary ? 'Resumen' : 'Resumen';
        },
    },
    methods: {
        updateSummaryEvaluation(resp) {
            this.summary.evaluation[resp.category] = resp.data;
        },
        updateSummaryEvaluationNotes(resp) {
            this.summary.evaluation[resp.category + 'Notes'] = resp.data;
        },
        updateSummaryAnthropometry(data) {
            this.summary.anthropometry = data;
        },
        updateSummaryTreatment(resp) {
            //this.summary.estreatment[resp.category] = resp.data;
            this.summary.estreatment.facial = resp.data.filter(
                (eva) => eva.category == 'facial'
            );
            this.summary.estreatment.corporal = resp.data.filter(
                (eva) => eva.category == 'corporal'
            );
            this.summary.estreatment.depilacion = resp.data.filter(
                (eva) => eva.category == 'depilacion'
            );
        },
        updateSummaryTreatmentNotes(resp) {
            this.summary.estreatment[resp.category + 'Notes'] = resp.data;
        },
        updateSummaryRecomendation(resp) {
            this.summary.recomendation[resp.category] = resp.data;
        },
        updateSummaryRecomendationNotes(resp) {
            this.summary.recomendation[resp.category + 'Notes'] = resp.data;
        },
    },
    created() {
        console.log('Component ready. summary');

        this.emitter.on('actSummaryEvaluation', this.updateSummaryEvaluation);
        this.emitter.on(
            'actSummaryEvaluationNotes',
            this.updateSummaryEvaluationNotes
        );
        this.emitter.on(
            'actSummaryAnthropometry',
            this.updateSummaryAnthropometry
        );
        this.emitter.on('actSummaryTreatment', this.updateSummaryTreatment);
        this.emitter.on(
            'actSummaryTreatmentNotes',
            this.updateSummaryTreatmentNotes
        );
        this.emitter.on(
            'actSummaryRecomendation',
            this.updateSummaryRecomendation
        );
        this.emitter.on(
            'actSummaryRecomendationNotes',
            this.updateSummaryRecomendationNotes
        );

        if (this.evaluations.length) {
            this.summary.evaluation.facial = this.evaluations.filter(
                (eva) => eva.category == 'facial'
            );
            this.summary.evaluation.corporal = this.evaluations.filter(
                (eva) => eva.category == 'corporal'
            );
            this.summary.evaluation.depilacion = this.evaluations.filter(
                (eva) => eva.category == 'depilacion'
            );
        }

        if (this.evaluationNotes.length) {
            this.summary.evaluation.facialNotes = this.evaluationNotes.filter(
                (eva) => eva.category == 'facial'
            )[0];
            this.summary.evaluation.corporalNotes = this.evaluationNotes.filter(
                (eva) => eva.category == 'corporal'
            )[0];
            this.summary.evaluation.depilacionNotes = this.evaluationNotes.filter(
                (eva) => eva.category == 'depilacion'
            )[0];
        }

        if (this.anthropometry) {
            this.summary.anthropometry = this.anthropometry;
        }
        if (this.estreatments.length) {
            this.summary.estreatment.facial = this.estreatments.filter(
                (eva) => eva.category == 'facial'
            );
            this.summary.estreatment.corporal = this.estreatments.filter(
                (eva) => eva.category == 'corporal'
            );
            this.summary.estreatment.depilacion = this.estreatments.filter(
                (eva) => eva.category == 'depilacion'
            );
        }
        if (this.treatmentNotes.length) {
            this.summary.estreatment.facialNotes = this.treatmentNotes.filter(
                (eva) => eva.category == 'facial'
            )[0];
            this.summary.estreatment.corporalNotes = this.treatmentNotes.filter(
                (eva) => eva.category == 'corporal'
            )[0];
            this.summary.estreatment.depilacionNotes = this.treatmentNotes.filter(
                (eva) => eva.category == 'depilacion'
            )[0];
        }
        if (this.recomendations.length) {
            this.summary.recomendation.facial = this.recomendations.filter(
                (eva) => eva.category == 'facial'
            );
            this.summary.recomendation.corporal = this.recomendations.filter(
                (eva) => eva.category == 'corporal'
            );
            this.summary.recomendation.depilacion = this.recomendations.filter(
                (eva) => eva.category == 'depilacion'
            );
        }
        if (this.recomendationNotes.length) {
            this.summary.recomendation.facialNotes = this.recomendationNotes.filter(
                (eva) => eva.category == 'facial'
            )[0];
            this.summary.recomendation.corporalNotes = this.recomendationNotes.filter(
                (eva) => eva.category == 'corporal'
            )[0];
            this.summary.recomendation.depilacionNotes =
        this.recomendationNotes.filter(
            (eva) => eva.category == 'depilacion'
        )[0];
        }
    },
};
</script>
