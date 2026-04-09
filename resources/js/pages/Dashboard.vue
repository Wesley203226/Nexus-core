<template>
  <div class="space-y-16 animate-in fade-in duration-1000">
    <!-- Hero Section -->
    <section class="text-center space-y-8 py-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full z-glass border-indigo-500/20 text-indigo-400 text-[8px] font-black uppercase tracking-[0.3em] animate-pulse mb-2">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span>
        Sistema Nexus Ativo
      </div>
      <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter leading-[0.85] mb-6">
        CONTROLE<br/><span class="text-transparent bg-clip-text bg-gradient-to-b from-indigo-400 to-indigo-900">CENTRAL</span>
      </h1>
      <p class="max-w-xl mx-auto text-slate-300 text-base font-medium leading-relaxed mb-8">
        A plataforma definitiva onde inteligência e inventário se encontram. 
        Gerencie recursos com precisão cirúrgica em um ambiente de alta performance.
      </p>
      <div class="flex flex-col sm:flex-row justify-center gap-4 pt-2">
        <router-link to="/products" class="z-btn group min-w-[180px]">
          <span>Ver Estoque</span>
          <ArrowRightIcon class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-500" />
        </router-link>
        <router-link to="/products/new" class="z-btn-secondary min-w-[180px]">
          <span>Novo Produto</span>
        </router-link>
      </div>
    </section>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="z-card p-6 space-y-3 group">
        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-500">
          <PackageIcon class="w-5 h-5" />
        </div>
        <div>
          <h3 class="text-slate-400 text-[8px] font-bold uppercase tracking-widest">Unidades Ativas</h3>
          <p class="text-4xl font-black text-white tabular-nums">{{ totalProducts }}</p>
        </div>
      </div>

      <div class="z-card p-6 space-y-3 group">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
          <DollarSignIcon class="w-5 h-5" />
        </div>
        <div>
          <h3 class="text-slate-400 text-[8px] font-bold uppercase tracking-widest">Valor Total</h3>
          <p class="text-4xl font-black text-white tabular-nums">R$ {{ totalValue }}</p>
        </div>
      </div>

      <div class="z-card p-6 space-y-3 group">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
          <GridIcon class="w-5 h-5" />
        </div>
        <div>
          <h3 class="text-slate-400 text-[8px] font-bold uppercase tracking-widest">Categorias</h3>
          <p class="text-4xl font-black text-white tabular-nums">{{ totalTypes }}</p>
        </div>
      </div>
    </div>

    <!-- Data Visualization -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
      <div class="lg:col-span-3 z-card p-10">
        <div class="flex justify-between items-center mb-10">
          <h2 class="text-xl font-black text-white uppercase tracking-tighter">Fluxo de Inventário</h2>
          <div class="flex gap-2">
            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
            <div class="w-2 h-2 rounded-full bg-white/10"></div>
          </div>
        </div>
        <div class="h-[350px]">
          <Bar v-if="chartData.datasets[0].data.length > 0" :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <div class="lg:col-span-2 z-card p-10 flex flex-col">
        <h2 class="text-xl font-black text-white uppercase tracking-tighter mb-10">Últimos Sinais</h2>
        <div class="space-y-6 flex-grow">
          <div v-for="product in recentProducts" :key="product.id" class="flex items-center justify-between group cursor-pointer">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center group-hover:border-indigo-500/50 transition-colors">
                <span class="text-xs font-bold text-slate-500">{{ product.name.charAt(0) }}</span>
              </div>
              <div>
                <p class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">{{ product.name }}</p>
                <p class="text-[10px] font-bold text-slate-500 uppercase">{{ formatDate(product.created_at) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-black text-white">R$ {{ formatPrice(product.price) }}</p>
              <div class="inline-block px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-black text-slate-400 uppercase tracking-tighter">
                {{ product.type?.name || 'Unidade' }}
              </div>
            </div>
          </div>
        </div>
        <router-link to="/products" class="mt-10 text-center text-[10px] font-black text-indigo-400 hover:text-white uppercase tracking-[0.2em] transition-colors">
          Ver Todas as Unidades →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { 
  Package as PackageIcon, 
  DollarSign as DollarSignIcon,
  Grid as GridIcon,
  ArrowRight as ArrowRightIcon
} from 'lucide-vue-next'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const products = ref([])
const types = ref([])
const loading = ref(true)

const totalProducts = computed(() => products.value.length)
const totalTypes = computed(() => types.value.length)
const totalValue = computed(() => {
  const sum = products.value.reduce((acc, p) => acc + (parseFloat(p.price) * p.quantity), 0)
  return sum.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
})

const recentProducts = computed(() => {
  return [...products.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5)
})

const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Unidades',
      backgroundColor: '#6366f1',
      hoverBackgroundColor: '#818cf8',
      borderRadius: 12,
      data: []
    }
  ]
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#030712',
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 12 },
      padding: 12,
      cornerRadius: 12,
      borderColor: 'rgba(255,255,255,0.1)',
      borderWidth: 1
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(255,255,255,0.03)', drawBorder: false },
      ticks: { color: '#64748b', font: { size: 10, weight: 'bold' } }
    },
    x: {
      grid: { display: false },
      ticks: { color: '#64748b', font: { size: 10, weight: 'bold' } }
    }
  }
}

const fetchData = async () => {
  try {
    const [pRes, tRes] = await Promise.all([
      axios.get('/api/products'),
      axios.get('/api/types')
    ])
    products.value = pRes.data
    types.value = tRes.data
    
    const typeCounts = {}
    products.value.forEach(p => {
      const typeName = p.type?.name || 'Outros'
      typeCounts[typeName] = (typeCounts[typeName] || 0) + p.quantity
    })
    
    chartData.value.labels = Object.keys(typeCounts)
    chartData.value.datasets[0].data = Object.values(typeCounts)
  } catch (error) {
    console.error('Data sync failed:', error)
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })
}

onMounted(fetchData)
</script>
