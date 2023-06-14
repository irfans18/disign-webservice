<div>
   <x-app-layout>
      <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Requests') }}
         </h2>
      </x-slot>

      <div class="py-12">
         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
               <div class="p-6 text-gray-900">

                  <div class="pb-12 pt-6">
                     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{-- <h3 class="mt-6 text-xl">Riwayat Token</h3> --}}
                        <div class="flex flex-col mt-6">
                           <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                              <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                 <div class="overflow-hidden border-b border-gray-200 rounded-md shadow-md">
                                    <table class="min-w-full overflow-x-scroll divide-y divide-gray-200">
                                       @isset($detail)
                                          <tbody class="bg-white divide-y divide-gray-200">
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Request From
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $user->name }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Contact Info
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $user->email }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Certificate ID
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->certificate_id }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Status
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->status_name }}
                                                </td>
                                             </tr>

                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Reason or Detail
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->revocation_detail }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Revoke at
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->formatted_revoked_at }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Revoke for
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->formatted_revoked_timestamp }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   Filename
                                                </td>
                                                <td class="px-6 py-4 text-sm text-black whitespace-nowrap">
                                                   {{ $detail->filepath }}
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                                {{-- Supported Document --}}

                                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                                                   Supported Document
                                                </td>
                                             </tr>
                                             <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">

                                                <td colspan="2"
                                                   class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                   <iframe src="{{ $pdf }}" class="w-full h-screen"></iframe>
                                                </td>
                                             </tr>
                                          </tbody>
                                       @endisset
                                    </table>
                                 </div>
                                 <div class="flex m-5 text-center justify-center">

                                    <div>
                                       <a class="m-2 px-4 py-1 bg-red-400 text-lg rounded-md text-white"
                                          wire:click="onDecline"
                                          href="/requests/{{$detail->id}}/dec">REJECT</a>
                                    </div>

                                    <div>
                                       <a class="m-2 px-2 py-1 bg-blue-400 text-lg rounded-md text-white"
                                          wire:click="onAccept"
                                          href="/requests/{{$detail->id}}/acc">APPROVE</a>
                                    </div>
                                    {{-- <a class="m-2 px-4 py-1 bg-red-400 text-lg rounded-md text-white"
                                    href="/dashboard/kelompok">REJECT</a>
                                 <a class="m-2 px-2 py-1 bg-blue-400 text-lg rounded-md text-white"
                                    href="/dashboard/kelompok">APPROVE</a> --}}
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </x-app-layout>
</div>
